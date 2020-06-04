window.fbAsyncInit = function() {
    // FB JavaScript SDK configuration and setup
    FB.init({
      appId      : '236049581019115', // FB App ID
      cookie     : true,  // enable cookies to allow the server to access the session
      xfbml      : true,  // parse social plugins on this page
      version    : 'v3.2' // use graph api version 2.8
    });
    
    // Check whether the user already logged in
    // FB.getLoginStatus(function(response) {
    //     if (response.status === 'connected') {
    //         //display user data
    //         getFbUserData();
    //     }
    // });
};

// Load the JavaScript SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Facebook login with JavaScript SDK
function fbLogin() {
    FB.login(function (response) {
        if (response.authResponse) {
            // Get and display the user profile data
            getFbUserData();
        }
    }, {scope: 'email'});
}

// Fetch the user profile data from facebook
function getFbUserData(){
    FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link'},
    function (response) {
        document.getElementById('fbLink').setAttribute("onclick","fbLogout()");
        if(!response.email){
            document.getElementById('email_id').value = '';
        }else{
             document.getElementById('email_id').value = response.email;
        }
        document.getElementById('first_last_name').value = response.first_name + ' ' + response.last_name;
        $('input[name=register]').trigger('click');
    });
}
 