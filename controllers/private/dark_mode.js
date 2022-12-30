
//Switch mode
const darkMode = () =>{ 
    localStorage.setItem("style_dark", true);
    document.getElementById('estilos').href = '../../resources/css/style_dark.css';
    document.getElementById("moosu").className += "bi bi-sun";
}
const lightMode = () =>{ 
    localStorage.setItem("style_dark", false);
    document.getElementById('estilos').href = ' '
    document.getElementById("moosu").className += "bi bi-moon-stars";
}

if(localStorage.getItem("style_dark") === "true"){
    darkMode()
}else{
    lightMode()
}

document.getElementById("moon").addEventListener("click", ()=>{
    if(localStorage.getItem("style_dark") === "true"){
        lightMode()
    }else{
        darkMode()
    }
})

