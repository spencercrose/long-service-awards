<?php

namespace App\Http\Controllers;

use App\Models\AccessibilityOption;
use App\Models\AccessibilityOptionAttendee;
use App\Models\DietaryRestriction;
use App\Models\DietaryRestrictionAttendee;
use App\Models\Guest;
use App\Models\Recipient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Attendee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Void_;

class AttendeeController extends Controller
{
    /**
     * @param int $aid The attendee id as passed through the URL.
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|null
     */
    public function rsvpBuild(int $aid)
    {
        // Get the recipient record, and related data.
        $data = (new Attendee)->getByRecipientId($aid);
        // Check if user should be allowed to RSVP
        if($data->status === 'invited') {
            // Grab list of dietary options
            $diet = DietaryRestriction::all('short_name');
            $data->diet = $diet;
            // Grab list of accessibiltiy options.
            $access = AccessibilityOption::all('short_name');
            $data->access = $access;
            // Push our id's to Session so we can use them when saving post data.
            Session::put('aid', $aid);

            return view('rsvp.rsvp')->with('data', $data);
        }
        // TODO: Handle error here
        return null;
    }

    /**
     * RSVP reply.
     * @param Request $request
     */
    protected function collectRsvp(Request $request) {
        // Validation first
        $validator = Validator::make($request->all(), [
            'rsvp' => 'required',
            // If they are bringing a guest, we need their guest names.
            'guest_first_name'=>'required_if:guest,on',
            'guest_last_name'=>'required_if:guest,on',
            'recip_access_other' =>'required_with:recip_access_checkbox,other|max:255',
            'guest_access_other' => '',
            'recip_diet_other' => '',
            'guest_diet_other' => '',
        ], $messages = [
            'required_if' => 'The :attribute field is required if have indicate you will have a guest.',
        ]);
        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                // Don't lose prior input.
                ->withInput();
        }
        //Original attendee record from path.
        $id = Session::get('aid');
        // Get Attendee record
        $attendee = Attendee::find($id);
        if($attendee === null){
            // Try to get attendee from recipient_id
            $attendee = Attendee::where('recipient_id', $request->recipient_id);
            // TODO: if we still can't find them, then we need to eject and throw an error.
        }

        // Update or add $guest record.
        if(isset($request->guest) && $request->guest == 'on') {
            // Put all guest logic in a function here.
            // Return guest_id after
            $guest_id = $this->guestManagement($attendee, $request);
        } else {
            $guest_id = null;
        }

        // Could be any number of diet requests - so we need to check them one by one to see if they should be updated or inserted.
        if(isset($request->recip_diet_checkbox)) {
            $rd_other = null;
            if(isset($request->recip_diet_other)) {
                $rd_other = $request->recip_diet_other;
            }
            $this->updateDietaryRecords($id, $request->recip_diet_checkbox, $rd_other);
        }
        // Guest dietary request
        if($guest_id !== null && isset($request->guest_diet_checkbox)) {
            $gd_other = null;
            if(isset($request->guest_diet_other)) {
                $gd_other = $request->guest_diet_other;
            }
            $this->updateDietaryRecords($guest_id, $request->guest_diet_checkbox, $gd_other);
        }

        // Update/add accessibility records.
        if(isset($request->recip_access_checkbox)) {
            $ra_other = null;
            if(isset($request->recip_access_other)) {
                $ra_other = $request->recip_access_other;
            }
            $this->updateAccessibilityRecords($id, $request->recip_access_checkbox, $ra_other);
        }
        // Guest accessibility record.
        if($guest_id !== null && isset($request->guest_access_checkbox)){
            $ga_other = null;
            if(isset($request->guest_access_other)) {
                $ga_other = $request->guest_access_other;
            }
            $this->updateAccessibilityRecords($guest_id, $request->guest_access_checkbox, $ga_other);
        }

        // Update RSVP status
        $rsvp =  $request->rsvp;
        if($rsvp) {
            $attendee->status = 'attending';
        } else {
            $attendee->status = 'declined';
        }
        // Update guest info
        if($guest_id !== null) {
            $attendee->guest_id = $guest_id;
        }
        // Save the attendee status
        $attendee->save();
        // TODO: Return confirmation and call for close.

        return $request->input();
    }

    /**
     * @param int $id - either the recip or guest id
     * @param array $choices - The users accessibility choice(s).
     */
    private function updateDietaryRecords($id, $choices, $other=null) {
        // Get all possible dietary choices and their ids
        // Check if user has previous record
        $dietary_prev = DietaryRestrictionAttendee::where('attendee_id', $id)->first();

        if($dietary_prev !== null) {
            //TODO: Remove previous records
        }
        $dietary_choices = DietaryRestriction::pluck('id', 'short_name')->toArray();
        foreach($choices as $key=>$value){
            // We need to remove the addition to the data
            // Get dietary id for user choice.
            if(array_key_exists($value, $dietary_choices)){
                $diet_id = $dietary_choices[$value];
            } else {
                //TODO handle error
                continue;
            }
            // Don't need to do anything if we already have this - just insert.
            $diet_record = new DietaryRestrictionAttendee;
            $diet_record->attendee_id = $id;
            $diet_record->dietary_restriction_id = $diet_id;
            if($other !== null){
                $diet_record->additional_details = $other;
            }
            // Todo - if option is other, we need to get that data.
            $diet_record->save();
        }
    }

    /**
     * @param int $id - either the recipient or guest id
     * @param array $choices - The users accessibility choice(s).
     */
    private function updateAccessibilityRecords($id, $choices, $other = null) {
        // Get all possible dietary choices and their ids
        // Check if user has previous record
        $recip_prev = AccessibilityOptionAttendee::where('attendee_id', $id)->first();

        if($recip_prev !== null) {
            //TODO: Remove previous records
        }
        $access_requirements = AccessibilityOption::pluck('id', 'short_name')->toArray();
        foreach($choices as $key=>$value){
            // Get dietary id for user choice.
            if(array_key_exists($value, $access_requirements)){
                $access_id = $access_requirements[$value];
            } else {
                //TODO handle error
                continue;
            }
            // Don't need to do anything if we already have this - just insert.
            $access_record = new AccessibilityOptionAttendee();
            $access_record->attendee_id = $id;
            $access_record->accessibility_option_id = $access_id;
            // If option is other, we need to get that data.
            if($other !== null){
                $access_record->additional_details = $other;
            }

            $access_record->save();
        }
    }

    private function guestManagement($attendee, $request) {
        $guest = Guest::find($attendee->guest_id);
        if($guest === null) {
            $guest = new Guest;
        }

        $guest->first_name = $request->guest_first_name;
        $guest->last_name = $request->guest_last_name;
        $guest->recipient_id = $attendee->recipient_id;
        $saved = $guest->save();

        if($saved){
            // Get id of new guest record.
            $guest_id = $guest->id;
        } else {
            // TODO: Error handling
            $guest_id = null;
        }
        // We also need update or add a guest attendee record
        $this->guestAttendee($attendee, $guest_id);

        return $guest_id;
    }

    private function guestAttendee($attendee, $guest_id)
    {
        // This is a guest record - we need to see if they exist yet.
        $guest_attendee = Attendee::where([
            'guest_id' => $guest_id,
            'type' => 'guest',
        ])->first();
        if ($guest_attendee === null) {
            // This is a new record
            $guest_attendee = new Attendee;
        }
        $guest_attendee->type = 'guest';
        $guest_attendee->guest_id = $guest_id;
        // Require carry over recipient data.
        $guest_attendee->recipient_id = $attendee->recipient_id;
        $guest_attendee->ceremony_id = $attendee->ceremony_id;
        $guest_attendee->status = 'attending';
        $guest_attendee->save();
    }

}
