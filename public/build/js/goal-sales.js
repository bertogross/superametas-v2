import { ToastAlert } from './helpers.js';

window.addEventListener('load', function () {
    const meantimeSelect = document.querySelector('select[name="meantime"]');
    const customMeantimeDiv= document.querySelector('.custom_meantime_is_selected');
    const customMeantimeInput = document.querySelector('.custom_meantime_is_selected input');

    function toggleCustomMeantimeInput() {
        const selectedOption = meantimeSelect.value;
        if (selectedOption === 'custom') {
            customMeantimeDiv.style.display = 'block';
        } else {
            customMeantimeDiv.style.display = 'none';

            if (customMeantimeInput) {
                customMeantimeInput.value = '';
            }
        }
    }

    toggleCustomMeantimeInput();

    meantimeSelect.addEventListener('change', toggleCustomMeantimeInput);

    // Initialize flatpickr
    const flatpickrRangeMonthElements = document.querySelectorAll('.flatpickr-range-month');

    if (flatpickrRangeMonthElements) {
        flatpickrRangeMonthElements.forEach(function (element) {
            flatpickr(element, {
                //dateFormat: 'F/Y',
                locale: 'pt',
                mode: "range",
                //noCalendar: true,
                allowInput: false,
                static: true,
                altInput: true,
                plugins: [
                    //https://flatpickr.js.org/plugins/
                    new monthSelectPlugin({
                        shorthand: true, //defaults to false
                        dateFormat: "Y-m", //defaults to "F Y"
                        altFormat: "F/Y", //defaults to "F Y"
                        theme: "dark" // defaults to "light"
                    })
                ]
            });
        });
    }
});
