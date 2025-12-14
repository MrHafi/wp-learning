<?php

////////////////////////////////////////////////////// REST API POST AND USER ON FORM SUBMISSION

add_shortcode( 'post_user_form', 'post_user_form' );
function post_user_form(){

?>

<div class="container mt-4">
    <div id="form-message" class="d-none  bg-primary">Result: </div>

    <form id="my-user-post-form" class="card p-4">
        <h4 class="mb-3">Create User + Post</h4>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="reg_email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Post Title</label>
            <input type="text" id="post_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Post Content</label>
            <textarea id="post_content" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    jQuery(function($){

        $('#my-user-post-form').on('submit', function(e){
            e.preventDefault(); 

            $.ajax({
                url: 'https://learning.devbuggs.com/wp-json/custom/my_api',
                method: 'POST',
                data: {
                    name: $('#name').val(),
                    email: $('#reg_email').val(),
                    title: $('#post_title').val(),
                    content: $('#post_content').val()
                },
                success: function(res){
                $('#form-message').removeClass('d-none').html(res.message);
                },
                
                error: function(){
                    $('#form-message').html('Something went wrong.');
                }
            });

        });

    });
});
</script>


<?php } //shortcode ended





// <!-- ADDING CUSTOM END POINTS -->


add_action ('rest_api_init', 'custom_route');
function custom_route(){

    $arg = [
        'methods'  => 'POST',
        'callback' => 'custom_route_callback',
        'permission_callback' => '__return_true'
    ];

    register_rest_route('custom', 'my_api', $arg );
}
            // CALLBACK FUNCTION
   function custom_route_callback($request){

    $data = $request->get_params();

    $name    = $data['name'] ?? '';
    $email   = $data['email'] ?? '';
    $title   = $data['title'] ?? '';
    $content = $data['content'] ?? '';


    $user = get_user_by('email', $email);

    // NEWE USER
    if( ! $user ){
        $user_id = wp_insert_user([
            'user_login' => $name,
            'user_pass'  => 'hello_world',
            'user_email' => $email
        ]);

            // ALREADY USER
            } else {
                $user_id = $user->ID;
            }

    // CREATE POST FOR BOTH CASES
    wp_insert_post([
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'draft',
        'post_author'  => $user_id
    ]);

    return [
  'success' => true,
  'message' => 'User and post added'
];

}


?>
