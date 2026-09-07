<!-- BEGIN: Footer-->
<footer class="footer {{ $configData['footerType'] === 'footer-hidden' ? 'd-none' : '' }} footer-light">
    <p class="clearfix mb-0">
        {{ date('Y') }}<a class="ml-25" href="{{ url('admin') }}" target="_blank">Appdid</a>
        <span class="d-none d-sm-inline-block">, All rights Reserved</span>
        </span>
        <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span>
    </p>
</footer>
<button class="btn btn-icon btn-icon rounded-circle btn-primary waves-effect waves-float waves-light scroll-top"
    type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->
