@extends('layouts.admin')

@section('content')
{{--
    Data: Object containing following values
        id: The attendee id.
        first_name: The first name of the recipient
        last_name: The last name of the recipient
        recipient_id: The recipients id
        type: The type of recipient this is (vip/exec etc.)
        guest_id: The id of the guest if the user already has one.
        ceremony_id: The id of the ceremony the user has been invited to.
        access: The accesibility options pulled from the database.
        diet: The dietary options pulled from the database.

--}}
<h1 >
    RSVP
</h1>
<div >
    <div >
        <h1> <strong> Hello {{$data->first_name . ' ' . $data->last_name }} </strong></h1>
        <div>
            <h2>Inclusivity</h2>
            <p>The Long Service Awards ceremonies are welcoming and accessible events.</p>
            <p>Government House has gender-neutral washroom facilities. Check the Venue Accessibility page for specific locations or contact [EMAIL] with questions.</p>
        </div>

        <form action="/rsvp" method="POST">
            @csrf
            {{-- RSVP acknowledgement --}}
            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="rsvp" id="rsvp" value="true" > I will be attending the LSA ceremonies</label><br />
                <label class="radio-inline">
                    <input type="radio" name="rsvp" id="rsvp" value="false" > I will not be attending the LSA ceremonies</label><br /><br />
                <!-- Error -->
                @if ($errors->has('rsvp'))
                    <div class="alert alert-danger">
                        {{ $errors->first('rsvp') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <input type="checkbox" name="guest" id="guest-rsvp"
                    @if( old('guest') === 'on')
                        checked
                    @endif
                "/>
                <label class="block font-medium text-sm text-gray-700"> A guest will be attending with me.</label><br /><br />
                <!-- Error -->
                @if ($errors->has('guest'))
                    <div class="alert alert-danger">
                        {{ $errors->first('guest') }}
                    </div>
                @endif
            </div>
            {{-- Should only show if guest check above is checked --}}
            <div class="form-group">
                <fieldset class="form-group" name="guest_name" id="guest_name_group">
                    <label> Guests First Name
                    <input type="text" name="guest_first_name"  value="{{ old('guest_first_name')}} "/>
                    </label>
                    <!-- Error -->
                    @if ($errors->has('guest_first_name'))
                        <div class="alert alert-danger">
                            {{ $errors->first('guest_first_name') }}
                        </div>
                    @endif
                    <br /><br />
                    <label> Guests Last Name
                        <input type="text" name="guest_last_name"  value="{{ old('guest_last_name')}}"/>
                    </label>
                        <!-- Error -->
                        @if ($errors->has('guest_last_name'))
                            <div class="alert alert-danger">
                                {{ $errors->first('guest_last_name') }}
                            </div>
                        @endif
                    <br /> <br />
                </fieldset>

            </div>
            {{-- Accessibility section --}}
            <div class="form-group">
                <fieldset class="form-group" name="accessibility" id="accessibility_group">
                    <input type="checkbox" name="recipient_access" id="recipient_access"
                            @if( old('recipient_access') === 'on')
                                checked
                            @endif
                    <label class="block font-medium text-sm text-gray-700">I will require accessibility considerations.</label><br /><br />
                    <fieldset class="form-group" name="access-group-recip" id="access_form_recipient">
                        <label>Accessibility Considerations for Recipient</label><br /><br />
                        {{-- Add in all accessibility restrictions in foreach --}}
                        @foreach($data->access as $access )
                            <input type="checkbox" name="recip_access_checkbox[]" value="{{$access->short_name}}"
                                @if( is_array(old('recip_access_checkbox')) && in_array($access->short_name, old('recip_access_checkbox')))
                                    checked
                                @endif
                            />
                            <label class="block font-medium text-sm text-gray-700"> {{$access->short_name}}</label><br />
                        @endforeach
                        <textarea cols="100" rows="6" name="recip_access_other" id="recip_access_other" >{{ old('recip_access_other')}}</textarea><br />
                        <label class="block font-medium text-sm text-gray-700">Please enter additional requirements (255 characters max)</label>
                    </fieldset><br /><br />
                    <input type="checkbox" name="guest_access" id="guest_access"
                        @if( old('guest_access') === 'on')
                           checked
                        @endif
                    />
                    <label class="block font-medium text-sm text-gray-700">My guest will require accessibility considerations.</label><br /><br />
                    <fieldset class="form-group" name="access-group-recip" id="accessible_form_guest">
                        <label>Accessibility Considerations for Guests</label><br /><br />
                        {{-- Add in all accessibility restrictions in foreach --}}
                        @foreach($data->access as $access )
                            <input type="checkbox" name="guest_access_checkbox[]" value="{{$access->short_name}}"
                                @if( is_array(old('guest_access_checkbox')) && in_array($access->short_name, old('guest_access_checkbox')))
                                   checked
                                @endif
                            />
                            <label class="block font-medium text-sm text-gray-700"> {{$access->short_name}}</label><br />
                        @endforeach
                        <textarea cols="100" rows="6" name="guest_access_other" id="guest_access_other" >{{ old('guest_access_other')}}</textarea><br />
                        <label class="block font-medium text-sm text-gray-700">Please enter additional requirements (255 characters max)</label>
                    </fieldset>
                </fieldset>
            </div><br /><br />


            {{-- Dietary section --}}
            <div class="form-group">
                <fieldset class="form-group" name="dietary" id="dietary_group">
                    <fieldset class="form-group" name="diet-group-recip" id="diet_form_recipient">
                        <input type="checkbox" name="recipient_diet" id="recipient_diet"
                            @if( old('recipient_diet') === 'on')
                               checked
                            @endif
                        />
                        <label class="block font-medium text-sm text-gray-700">I will require dietary considerations.</label><br /><br />
                        <label>Dietary Considerations for Recipient</label><br /><br />
                        {{-- Add in all dietary restrictions in foreach --}}
                        @foreach($data->diet as $diet )
                            <input type="checkbox" name="recip_diet_checkbox[{{ $diet->id }}]" value="{{$diet->short_name}}"
                                   @if (is_array(old('recip_diet_checkbox')) && in_array($diet->short_name, old('recip_diet_checkbox')))
                                   checked
                                @endif
                            />
                            <label class="block font-medium text-sm text-gray-700"> {{$diet->short_name}}</label><br />
                        @endforeach
                        <textarea cols="100" rows="6" name="recip_diet_other" id="recip_diet_other" >{{ old('recip_diet_other')}}</textarea><br />
                        <label class="block font-medium text-sm text-gray-700">Please enter additional requirements (255 characters max)</label>

                    </fieldset><br /><br />

                    <fieldset class="form-group" name="diet-group-diet" id="diet_form_guest">
                        <input type="checkbox" name="guest_diet" id="guest_diet"
                               @if( old('guest_diet') === 'on')
                               checked
                            @endif
                        />
                        <label class="block font-medium text-sm text-gray-700">My guest will require dietary considerations.</label><br /><br />

                        <label>Dietary Restrictions for Guests</label><br /><br />
                        {{-- Add in all dietary restrictions in foreach --}}
                        @foreach($data->diet as $diet )
                            <input type="checkbox" name="guest_diet_checkbox[]" value="{{$diet->short_name}}"
                                @if ( is_array(old('guest_diet_checkbox')) && in_array($diet->short_name, old('guest_diet_checkbox')))
                                   checked
                                @endif
                            />
                            <label class="block font-medium text-sm text-gray-700"> {{$diet->short_name}}</label><br />
                        @endforeach
                        <textarea name="guest_diet_other" id="guest_diet_other" cols="100" rows="6"  >{{ old('guest_diet_other')}} </textarea><br />
                        <label class="block font-medium text-sm text-gray-700">Please enter additional requirements (255 characters max)</label>
                    </fieldset>
                </fieldset>
            </div>
            <div class="form-group">
                <x-button class="ml-3">
                    {{ __('Submit') }}
                </x-button>
            </div>

        </form>
    </div>
</div>
@endsection('content')
