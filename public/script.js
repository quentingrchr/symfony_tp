/* Reset form value */
const $forms = document.querySelectorAll('form');
$forms.forEach( $form =>{
    const $resetBtn = $form.querySelector('[data-reset-form-value]');
    const $resetInputs = $form.querySelectorAll('[data-old-value]');

    $resetBtn.addEventListener('click',(e)=>{
        e.preventDefault();
        $resetInputs.forEach($input => {
            $input.value = $input.getAttribute('data-old-value');
        })
    })
})


