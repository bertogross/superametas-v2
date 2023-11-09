/**
 * https://github.com/Choices-js/Choices
 * @param {*} SearchURL
 * @param {*} StoreOrUpdateURL
 * @param {*} choicesSelectorClass
 */
export function choicesListeners(SearchURL, StoreOrUpdateURL, choicesSelectorClass) {
    if (SearchURL && StoreOrUpdateURL && choicesSelectorClass) {
        // Initialize Choices for each select element with the class 'term-choice'
        var choicesSelector = document.querySelectorAll(choicesSelectorClass);
        if (choicesSelector) {
            choicesSelector.forEach(function (select) {

                /*
                var selectId = select.id;
                //console.log('selectId: ', selectId);
                let isChoiceEl = document.getElementById(selectId);
                */

                var choicesInstance = new Choices(select, {
                    removeItems: true,
                    removeItemButton: true,
                    duplicateItemsAllowed: false,
                    noResultsText: 'Nenhum tópico encontrado',
                    noChoicesText: 'Sem opções a escolher',
                    itemSelectText: '',
                    allowHTML: true,
                    addItems: true,
                    editItems: true,
                    paste: false,
                    addItemText: (value) => {
                        return `Pressione Enter para adicionar <b>"${value}"</b>`;
                    },
                    maxItemText: (maxItemCount) => {
                        return `Apenas ${maxItemCount} das opções pode(m) ser selecionada(s)`;
                    }
                });

                // Function to fetch data and update Choices.js for the specific select element
                function fetchAndPopulateData(query, currentChoicesInstance) {
                    fetch(SearchURL + `?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data search fetched:', data);
                            //console.log('Current Choices Instance:', currentChoicesInstance);

                            // Clear existing choices before setting new ones if needed
                            currentChoicesInstance.clearChoices();

                            currentChoicesInstance.setChoices(data.map(term => (
                                { value: term.id, label: term.name, selected: true, disabled: false}
                            )), 'value', 'label', false );

                        })
                        .catch(error => console.error('Error fetching search terms:', error)
                    );
                }

                // Listen for when the user types and fetch data for the specific select element
                select.addEventListener(
                    'search', function (event) {
                        //console.log('search event:', event);

                        if (event.detail.value) {
                            fetchAndPopulateData(event.detail.value, choicesInstance);
                        }
                    }
                );

                /*
                // Listen for when a new item is added (if you allow adding new terms)
                select.addEventListener(
                    'addItem', function (event) {
                        console.log('addItem event:', event.detail);

                        // The addItem event should be listened on the select element, not on the passedElement.element
                        var value = event.detail.value;
                        var label = event.detail.label;

                        if (label) {
                            var newTerm = { name: label };
                            fetch(StoreOrUpdateURL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(newTerm)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    choicesInstance.clearChoices();

                                    choicesInstance.setChoices([{
                                        value: data.term.id,
                                        label: data.term.name,
                                        selected: true,
                                        disabled: false,
                                    }], 'value', 'label', false);
                                }
                            })
                            .catch(error => console.error('Error during fetch addItem:', error));
                        }
                    }
                );
                */
            });
        }
    }
}
