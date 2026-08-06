@extends('layouts.app')
@section('content')
<div class="pad100 bg-grey">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-12 text-center">
                          <h2 class="heading wow animate fadeInUp">Smooth Procedure For<br> <span class="txt-color">Our Customers </span> </h2>
                    </div>
            <div class="col-md-3 text-center">
              <div class="process-thumb">
                <img src="{{asset('admin/asset/images/icons/web.png')}}" width="60">
                <h6>Apply Online</h6>
                <p>Fill out our online application form to request our services</p>
              </div>
            </div>

            <div class="col-md-3 text-center">
              <div class="process-thumb">
                <img src="{{asset('admin/asset/images/icons/document.png')}}" width="60">
                <h6>Documentation</h6>
                <p>Our team will check your documentation to make sure it meets our requirements.</p>
              </div>
            </div>

             <div class="col-md-3 text-center">
              <div class="process-thumb">
                <img src="{{asset('admin/asset/images/icons/call-center.png')}}" width="60">
                <h6>Processing</h6>
                <p>After reviewing your document our team will get in touch with you. </p>
              </div>
            </div>

             <div class="col-md-3 text-center">
              <div class="process-thumb">
                <img src="{{asset('admin/asset/images/icons/package.png')}}" width="60">
                <h6>Destination</h6>
                <p>Your package is ready and will be reached on time.  </p>
              </div>
            </div>
     </div>
      </div>
  </div>
@endsection
