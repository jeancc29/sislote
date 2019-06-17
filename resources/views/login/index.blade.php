@extends('header')

@section('content')
        


        <div class="wrapper wrapper-full-page">
          
            
















        <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('{{asset('assets/img/login.jpg')}}'); background-size: cover; background-position: top center;">
            <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
            <div class="container">
              <div class="col-lg-5 col-sm-7 ml-auto mr-auto">
                <form class="form" method="post" action="{{url('login/acceder')}}">
                    {{csrf_field()}}
                  <div class="card card-login"> <!-- card-hidden -->
                    <div class="card-header card-header-rose text-center">
                      <h4 class="card-title">Login</h4>
                      <div class="social-line">
                        <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                          <i class="fa fa-facebook-square"></i>
                        </a>
                        <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                          <i class="fa fa-twitter"></i>
                        </a>
                        <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                          <i class="fa fa-google-plus"></i>
                        </a>
                      </div>
                    </div>
                    <div class="card-body ">
                      <p class="card-description text-center">Or Be Classical</p>
                      <span class="bmd-form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="material-icons">face</i>
                            </span>
                          </div>
                          <input type="text" class="form-control" name="usuario" placeholder="Usuario..." value="{{old('name')}}">
                        </div>
                      </span>
                      
                      <span class="bmd-form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="material-icons">lock_outline</i>
                            </span>
                          </div>
                          <input type="password" class="form-control" name="password" placeholder="Password...">
                          <input type="hidden">
                        </div>
                      </span>
                    </div>
                    <div class="card-footer justify-content-center">
                        <input type="submit" class="btn btn-info btn-link btn-lg" name="login" value="Lets Go">
                      <!-- <a href="#pablo" class="btn btn-rose btn-link btn-lg">
                          Lets Go
                          <input type="hidden">
                      </a> -->
          
                      
                    </div>

                   
                        @if($errors->any())
                        <div class="row justify-content-center">
                          <div class="col-10">
                            <div class="alert alert-danger" role="alert">
                              @foreach($errors->all() as $e)
                                  <ul>
                                      <li>{{ $e }}</li>
                                  </ul>
                              @endforeach
                            </div>
                          </div>
                        </div>
                        @endif
                    

                  </div>
                </form>
              </div>
            </div>
          
          
          </div>
          
                    
                  </div>
                  
                  
          
          
          
          
          
          
          
          
          
          
            <script>
            $(document).ready(function(){
              demo.checkFullPageBackgroundImage();setTimeout(function(){
                  // after 1000 ms we add the class animated to the login/register card
                  $('.card').removeClass('card-hidden');
                }, 700);});
          </script>
          
          
          
              </body>
          
          </html>
          @endsection