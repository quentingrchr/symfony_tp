/* Reset form value */
const $forms = document.querySelectorAll('form');
$forms.forEach( $form =>{
    const $resetBtn = $form.querySelector('[data-reset-form-value]');
    const $resetInputs = $form.querySelectorAll('[data-old-value]');
    if(!$resetBtn) return

    $resetBtn.addEventListener('click',(e)=>{
        e.preventDefault();
        $resetInputs.forEach($input => {
            $input.value = $input.getAttribute('data-old-value');
        })
    })
})

/* Profile page filter
 */
const $profilePostsContainer = document.querySelector("#your-post")
const $profileForms = document.querySelectorAll(".form-check-input")
let displayState = {
    'draft': true,
    'published': true
}
$profileForms.forEach(($form)=> {
    $form.addEventListener('change', (e) => {
        if(e.target.value){
            displayState[e.target.value] = !displayState[e.target.value]
        }
        updateFilter(displayState)
    })
})
function updateFilter(state){
  const $posts =  $profilePostsContainer.querySelectorAll("[data-draft]")
    $posts.forEach($post => {

        const isDraft = $post.getAttribute('data-draft')

        if(isDraft === "true") {
            console.log($post, 'draft')
            state.draft ? $post.classList.remove('hidden') :  $post.classList.add('hidden');

        } else {
            // isPublished
            state.published ? $post.classList.remove('hidden') :  $post.classList.add('hidden');
        }
    })
}

