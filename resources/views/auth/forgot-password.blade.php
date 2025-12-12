<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/grapmult_logo.png">

    <title>Grapmult MD - Récupérer le Mot de Passe</title>
  
    <!-- Vendors Style-->
   <link rel="stylesheet" href="{{ asset('backend/css/vendors_css.css') }}">
      
    <!-- Style-->  
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/skin_color.css') }}">

</head>

<body class="hold-transition theme-primary bg-gradient-primary">
    
    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">
            
            <div class="col-12">
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-4 col-md-5 col-12">
                        <div class="content-top-agile p-10">
                            <h3 class="mb-0 text-white">Récupérer le Mot de Passe</h3>                               
                        </div>
                        <div class="p-30 rounded30 box-shadowed b-2 b-dashed">

                            <div class="mb-4 text-sm text-gray-600">
            {{ __('Mot de passe oublié ? Pas de problème. Il vous suffit de nous indiquer votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d\'en choisir un nouveau.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
                                
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent text-white"><i class="ti-email"></i></span>
                                        </div>
                                        <input type="email" class="form-control pl-15 bg-transparent text-white plc-white" placeholder="Votre E-mail">
                                    </div>
                                </div>
                                  <div class="row">
                                    <div class="col-12 text-center">
                                      <button type="submit" class="btn btn-info btn-rounded margin-top-10">Envoyer le Lien de Réinitialisation du Mot de Passe par E-mail</button>
                                    </div>
                                    <!-- /.col -->
                                  </div>
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  



    <!-- Vendor JS -->
   <script src="{{ asset('backend/js/vendors.min.js') }}"></script>
    <script src="{{ asset('../assets/icons/feather-icons/feather.min.js') }}"></script>    
    

</body>
</html>
