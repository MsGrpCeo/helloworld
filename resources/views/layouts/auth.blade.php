<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body class="page-header-fixed login">

    <div class="container-fluid">
        @yield('content')
    </div>

    <div class="scroll-to-top"
         style="display: none;">
        <i class="fa fa-arrow-up"></i>
    </div>

    @include('partials.javascripts')
</body>
<style>
    .btn.btn-outline.green.active, .btn.btn-outline.green:active, .btn.btn-outline.green:active:focus, .btn.btn-outline.green:active:hover, .btn.btn-outline.green:focus, .btn.btn-outline.green:hover {
        border-color: #FFFFFF;
        color: #FFF;
        background-color: var(--third-color);
    }
    @media (max-width: 979px) {
        /* .login .content {
            width: 100%;
            margin-top: 20vh;
        } */
        .login .content .forget-form, .login .content .login-form {
            display: block;
            width: 100%;
        }
        .form-inline .control-label, .form-inline .form-group {
            width: 100%;
            padding-right: 0;
        }
        .login .logo img {
            width: 100%;
        }
        .login .content .form-actions {
            width: 100%;
            padding: 0 0 25px;
            margin-left: 0;
        }
        .p-signup {
            display: flex;
            justify-content: space-between;
        }
    }
</style>
</html>