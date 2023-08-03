/**
 * Vvveb
 *
 * Copyright (C) 2021  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */
 
 
import {Router} from './common/router.js';
import {Themes} from './admin/controller/themes.js';
import {Plugins} from './admin/controller/plugins.js';
import {Table} from './admin/controller/table.js';
import {HeartBeat} from './admin/heartbeat.js';

if (window.Vvveb === undefined) window.Vvveb = {};

window.themes = Themes;
window.plugins = Plugins;
window.table = Table;

window.delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

//let theme = localStorage.getItem("theme", "dark");
let theme = $("html").attr("data-bs-theme");
if (theme) {
	if (theme == "dark") {
		$("#color-theme-switch i").removeClass("la-sun").addClass("la-moon");
	}
	//$("html").attr("data-bs-theme", theme);
}

//let smallNav = localStorage.getItem("sidebar-size", "small-nav");
let smallNav = document.cookie.indexOf("sidebar-size=small-nav") > 0;
if (smallNav) {
	$("#container").addClass(smallNav);
}

//$.ajax(window.location.pathname + '?action=heartbeat');

function toggleClass(container, className) {
}

jQuery(document).ready(function() {
	Router.init();
	
	jQuery(".menu-toggle").click(function() {  
		if ($("#container").hasClass("small-nav")) {
			$("#container").removeClass("small-nav");
			smallNav = ""; 
		} else {
			smallNav = "small-nav";
			$("#container").addClass(smallNav);
		}
		
		//localStorage.setItem('sidebar-size', smallNav);
		document.cookie = "sidebar-size=" + smallNav + ";path=/;domain=." + window.location.host.replace(/^.*?\./, '') + ";";
	});
	
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
		document.cookie = "theme=" + theme + ";path=/;domain=." + window.location.host.replace(/^.*?\./, '') + ";";
		//serverStorage.setItem();
	});
});

class Chain {
    constructor() {
        this.start = false;
        this.stack = [];
    }

    add(call) {
        this.stack.push(call);

        if (!this.start) {
            this.execute();
        }
    }

    execute() {
        if (this.start = this.stack.length) {
            var call = this.stack.shift();
            var ajax = call();

            ajax.done(function() {
                chain.execute();
            });
        }
    }
}

window.chain = new Chain();

function ucFirst(str) {
  if (!str) return str;

  return str[0].toUpperCase() + str.slice(1);
}
