// Iniciador del elemento cargar ---------.
window.onload = function(){
    var contenedor = document.getElementById('contenedor_carga');
    contenedor.style.visibility = 'hidden';
    contenedor.style.opacity = '0';
}

//Nav bar fixed
navbar = document.getElementById('navbar-dashboard');

window.addEventListener('scroll', function () {
    if (window.pageYOffset > 200) {
        navbar.classList.add('nav-fixed', 'shadow', 'fixed-top');
    } else {
        navbar.classList.remove('nav-fixed', 'shadow', 'fixed-top');
    }
});
