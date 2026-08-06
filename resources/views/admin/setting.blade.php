
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <header class="header">

            </header>
            <div class="form-wrap">
                <form id="survey-form"  >
                    @csrf
                    <div> <h3 id="title" class="title ">Settings</h3></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="name-label" for="name">Website Name</label>
                                <input type="text" name="site_name" id="name" placeholder="Enter your name" class="form-control" value="{{--$setting?$setting->site_name:''--}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="logo-label" for="logo">Logo</label>
                                <input type="file" name="logo" id="logo"  class="form-control" required>
                                <div>
                                    <img src="{{$setting?$setting->logo:''}}" alt="" id="">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="phone-label" for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" placeholder="Enter your phone" class="form-control" value="{{--$setting?$setting->phone:''--}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="email-label" for="email">Email</label>
                                <input type="email" name="email" id="email" placeholder="Enter your email" class="form-control" value="{{--$setting?$setting->email:''--}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="address-label" for="address">Address</label>
                                <input type="address" name="address" id="address" placeholder="Enter your address" class="form-control" value="{{--$setting?$setting->address:''--}}" required>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-danger">Social links <small>(Full Url)</small> </h4>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="facbook-label" for="facbook">Facebook </label>
                                <input type="text" name="facebook" id="facbook" min="10" max="99" class="form-control" placeholder="Facebook" value="{{--$setting?$setting->facebook:''--}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="instagram-label" for="instagram">Instagram </label>
                                <input type="text" name="instagram" id="instagram" min="10" max="99" class="form-control" placeholder="Instagram" value="{{--$setting?$setting->instagram:''--}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="twitter-label" for="twitter">Twitter</label>
                                <input type="text" name="twitter" id="twitter" min="10" max="99" class="form-control" placeholder="X" value="{{--$setting?$setting->twitter:''--}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="linkedin-label" for="linkedin">LinkedIn </label>
                                <input type="text" name="linkedin" id="linkedin" min="10" max="99" class="form-control" placeholder="LinkedIn" value="{{--$setting?$setting->linkedin:''--}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="youtube-label" for="youtube">Youtube </label>
                                <input type="text" name="youtube" id="youtube" min="10" max="99" class="form-control" placeholder="Youtube"     value="{{--$setting?$setting->youtube:''--}}">
                            </div>
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Would you recommend survey to a friend?</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline1" value="Definitely" name="customRadioInline1" class="custom-control-input" checked="">
                                    <label class="custom-control-label" for="customRadioInline1">Definitely</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline2" value="Maybe" name="customRadioInline1" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadioInline2">Maybe</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline3" value="Not sure" name="customRadioInline1" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadioInline3">Not sure</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>This survey useful yes or no?</label>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" name="yes" value="yes" id="yes" checked="">
                                    <label class="custom-control-label" for="yes">Yes</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" name="no" value="no" id="no">
                                    <label class="custom-control-label" for="no">No</label>
                                </div>
                            </div>
                        </div>
                    </div> --}}


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Google Map</label>
                                <textarea  id="map" class="form-control" name="map" placeholder="Enter map ifram here...">{{--$setting?$setting->map:''--}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <button type="submit" id="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<!--Script Start Here-->
<script>
    $("#survey-form").on("submit", function(e) {
        e.preventDefault();  // stop normal form submit

        var formData = new FormData(this);

        $.ajax({
            url: "/",     // your server script
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(response) {

                if(response.status === 'success'){
                    toastr.success(response.message);
                } else if(response.status === 'error'){
                    $.each(response.errors, function(key, value){
                        toastr.error(value);
                    });
                }
            },
            error: function(err) {
                toastr.error("Error:", err);
                toastr.error("Something went wrong!");
            }
        });

    });

</script>
<!--Script End Here-->

@endsection
