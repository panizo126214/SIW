function validate_register(){

    var username = document.getElementById("username");
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirmPassword");
    var error = false;
    var mensajeError = "";

    if(username.value.length < 1){
        error = true;
        username.style.backgroundColor = 'red';
        mensajeError = mensajeError + "El nombre de usuario no debe dejarse en blanco.\n";
    }
    else{
        username.style.backgroundColor = 'white';
    }
    if(email.value.length < 1){
        error = true;
        email.style.backgroundColor = 'red';
        mensajeError = mensajeError + "El email no debe dejarse en blanco.\n";
    }
    else{
        email.style.backgroundColor = 'white';
    }
    if(password.value.length < 3){
        error = true;
        password.style.backgroundColor = 'red';
        mensajeError = mensajeError + "La password debe tener al menos 3 caracteres.\n";
    }
    else{
        password.style.backgroundColor = 'white';
    }
    if(confirmPassword.value.length < 3){
        error = true;
        confirmPassword.style.backgroundColor = 'red';
        mensajeError = mensajeError + "La password debe ser confirmada.\n";
    }
    else{
        confirmPassword.style.backgroundColor = 'white';
    }

    if(password.value !== confirmPassword.value){
        password.style.backgroundColor = 'red';
        confirmPassword.style.backgroundColor = 'red';
        mensajeError = mensajeError + "Las passwords no coinciden.\n";
        error = true;
    }

    if(error == true){
        alert(mensajeError);
    }
    else{
        document.getElementById("form_register").submit();
    }


}

