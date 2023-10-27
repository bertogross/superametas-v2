<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

@yield('script')
@yield('script-bottom')

<script>
// Prevent data-choices sort comapnies by name
var isChoiceEl = document.getElementById("filter-companies");
if(isChoiceEl){
    var choices = new Choices(isChoiceEl, {
        shouldSort: false,
        removeItems: true,
        removeItemButton: true
    });
}
</script>

<script>
window.App = {!! json_encode([
    'url' => URL::asset('/'),
]) !!};
</script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
