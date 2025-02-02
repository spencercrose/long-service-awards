<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Long Service Awards - RSVP</title>

    <style>

        .xxl {
            font-size: 42pt;
        }
        .very-lorge {
            font-size: 30pt;
        }
        .lorge {
            font-size: 18pt;
        }
        .medium {
            font-size: 12pt;
        }
        .center {
            text-align: center;
        }
        #recipient-name {
            line-height: 40pt;
        }
        .left {
            text-align: left;
        }
        .grey-bg {
            background-color: #F3F3F3;
        }
    </style>

</head>
<body>


<div>
    <div class="container">
    @if ($recipient->attendee->status == 'declined')


        <div class="row">
            <div class="col-1">
                &nbsp;
            </div>
            <div class="col-10 center grey-bg">
                <img src="{{url('/img/banner-w-coat.png')}}" class="img-fluid" style="margin-top: 10pt">
                    <p class="xxl">Thank you {{$recipient->first_name}}, and congratulations!</p>
                    <p class="lorge">Following the Long Service Awards ceremonies, your award will be mailed to the office address you provided:</p>

                    <p>
                        {{$recipient->office_address_prefix ?? ''}} <br>
                        {{$recipient->office_address_suite ?? ''}} <br>
                        {{$recipient->office_address_street_address ?? ''}} <br>
                        {{$recipient->office_address_postal_code ?? ''}} <br>
                        {{$recipient->officeCommunity->name ?? ''}}
                    </p>
            </div>
            <div class="col-1">
                &nbsp;
            </div>

        </div>

    @else
    {{-- User declined. --}}

            <div class="row">
                <div class="col-1">
                    &nbsp;
                </div>
                <div class="col-10 center grey-bg">
                    <img src="{{url('/img/banner-w-coat.png')}}" class="img-fluid" style="margin-top: 10pt">
                    <p class="xxl">Thank you {{$recipient->first_name}}, and congratulations!</p>
                    <p class="lorge">The ceremony will take place at Government House</p>
                    <p class="lorge">Dress code: Business Attire.</p>
                    <p class="lorge">Doors open at 5:45 PM.</p>
                    <p class="lorge">Your invitation is not required for entry.</p>
                    <p>Visit the <a href="https://longserviceawards.gww.gov.bc.ca/ceremony/" target="_blank">Long Service Awards website</a> for the most current ceremony information.</p>

                    @if($recipient->attendee->accessibilityOptions->count() > 0)
                        <p>You indicated your accessibility requirements are:</p>
                        <br>
                            @foreach($recipient->attendee->accessibilityOptions as $accessibilityOption)
                                <p>{{$accessibilityOption->description}}</p>
                            @endforeach
                        <br>
                    @endif

                    @if(!empty($recipient->guest->attendee) && $recipient->guest->attendee->accessibilityOptions->count() > 0)
                        <p>You indicated your guest's accessibility requirements are:</p>
                        <br>
                            @foreach($recipient->guest->attendee->accessibilityOptions as $accessibilityOption)
                                <p>{{$accessibilityOption->description}}</p>
                            @endforeach
                        <br>
                    @endif

                    @if($recipient->attendee->dietaryRestrictions->count() > 0)
                        <p>You indicated your dietary requirements are:</p>
                        <br>
                            @foreach($recipient->attendee->dietaryRestrictions as $dietaryRestriction)
                                <p>{{$dietaryRestriction->short_name}}</p>
                            @endforeach
                        <br>
                    @endif

                    @if(!empty($recipient->guest->attendee) && $recipient->guest->attendee->dietaryRestrictions->count() > 0)
                        <p>You indicated your guest's  dietary requirements are:</p>
                        <br>
                            @foreach($recipient->guest->attendee->dietaryRestrictions as $dietaryRestriction)
                                <p>{{$dietaryRestriction->short_name}}</p>
                            @endforeach
                        <br>
                    @endif

                    @if($updated_contact)
                        <p>You updated your current contact information.</p>
                    @endif



                    <div class="row">
                        <div class="col-1">

                        </div>
                        <div class="col-10">
                            <p class="medium">If you need to make changes to your RSVP information or cancel your attendance, please <a href="mailto:LongServiceAwards@gov.bc.ca">email the Long Service Awards</a> team as soon as possible.</p>
                            <p class="medium">For information about travel reimbursement and taking time off, visit the <a href="https://longserviceawards.gww.gov.bc.ca/" target="_blank">Long Service Awards website</a> or email your <a href="https://longserviceawards.gww.gov.bc.ca/contacts/" target="_blank">workplace contact</a>. If you have questions about the ceremony, <a href="mailto:LongServiceAwards@gov.bc.ca">email the Long Service Awards team</a>.</p>
                            <p class="medium"><strong>COVID-19 Pandemic</strong></p>
                            <p class="medium">The Long Service Awards program is closely following the Provincial Health Officer’s directives regarding events and travel. Due to the evolving nature of the COVID-19 pandemic, the upcoming award ceremonies are still to be confirmed. The program area will provide updates over the coming months. Thank you for your patience during this unprecedented time.</p>

                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                                    </div>
                             </div>
                <div class="col-1">
                    &nbsp;
                </div>

            </div>

    @endif

</div>

</div>
<!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>


</body>
</html>
