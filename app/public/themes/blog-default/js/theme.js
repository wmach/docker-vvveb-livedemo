/*
 * Sticky navbar
 * 
 */
  	
// When the user scrolls the page, execute navbarSticky
window.onscroll = function() {navbarSticky()};

// Get the navbar
var navbar = document.getElementsByClassName("navbar")[0];

// Get the offset position of the navbar
var sticky = navbar.offsetTop ? navbar.offsetTop : navbar.offsetHeight;

function toggleNavbarTheme () {
    if (navbar.classList.contains("navbar-dark")) {
		navbar.classList.add("navbar-light");
		navbar.classList.remove("navbar-dark");
	} else if (navbar.classList.contains("navbar-light")) {
		navbar.classList.add("navbar-dark");
		navbar.classList.remove("navbar-light");
	}
}


// Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
function navbarSticky() {
  let isSticky = (window.pageYOffset >= sticky);		
		
  if (isSticky) {
	  if (!navbar.classList.contains("sticky")) {
		navbar.classList.add("sticky");
		toggleNavbarTheme();
	  } 
  } else {
	  if (navbar.classList.contains("sticky")) {
		navbar.classList.remove("sticky");
		toggleNavbarTheme();
	  }
  }
}

let theme = $("html").attr("data-bs-theme");
if (theme) {
	if (theme == "dark") {
		$("#color-theme-switch i").removeClass("la-sun").addClass("la-moon");
	}
	//$("html").attr("data-bs-theme", theme);
}

$("#color-theme-switch").click(function () {
	
	let theme = $("html").attr("data-bs-theme");
	
	if (theme == "dark") {
		theme = "light";
		$("i",this).removeClass("la-moon").addClass("la-sun");
	} else if (theme == "light" || theme == "auto" || !theme) {
		theme = "dark";
		$("i", this).removeClass("la-sun").addClass("la-moon");
	} else {
		theme = "auto";
	}

	$("html").attr("data-bs-theme", theme);
	//localStorage.setItem("theme", theme);
	document.cookie = "theme=" + theme + ";path=/;";
	//serverStorage.setItem();
});