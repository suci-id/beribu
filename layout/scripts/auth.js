let buttonNav = document.getElementById("logauth_button_nav");

function authLogin(){
    const username = document.getElementById('usernameLoginAuth').value;
    const password = document.getElementById('passwordLoginAuth').value;
    document.getElementById('usernameLoginAuth').value = "";
    document.getElementById('passwordLoginAuth').value = "";

    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
    
    const urlencoded = new URLSearchParams();
    urlencoded.append("status_auth", "login");
    urlencoded.append("username", username);
    urlencoded.append("password", password);

    const requestOptions = {
      method: "POST",
      headers: myHeaders,
      body: urlencoded,
      redirect: "follow"
    };
    
    fetch("http://203.194.112.48/api/auth.php", requestOptions)
        .then((response) => response.json())
        .then((result) => {
            sessionStorage.setItem("username", result.username)
            console.log(result)
            buttonNav.removeChild(buttonNav.lastElementChild)
            closeForm()
            pastikanLogin()
        })
        .catch((error) => console.error(error));
}

function authLogout(){
    sessionStorage.removeItem("username");
    buttonNav.removeChild(buttonNav.lastElementChild)
    closeForm()
    pastikanLogin()
    // localStorage.removeItem("json_result");
    // pastikanLogin()
}
