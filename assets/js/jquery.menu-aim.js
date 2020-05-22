/**
 * menu-aim is a jQuery plugin for dropdown menus that can differentiate
 * between a user trying hover over a dropdown item vs trying to navigate into
 * a submenu's contents.
 *
 * menu-aim assumes that you have are using a menu with submenus that expand
 * to the menu's right. It will fire events when the user's mouse enters a new
 * dropdown item *and* when that item is being intentionally hovered over.
 *
 * __________________________
 * | Monkeys  >|   Gorilla  |
 * | Gorillas >|   Content  |
 * | Chimps   >|   Here     |
 * |___________|____________|
 *
 * In the above example, "Gorillas" is selected and its submenu content is
 * being shown on the right. Imagine that the user's cursor is hovering over
 * "Gorillas." When they move their mouse into the "Gorilla Content" area, they
 * may briefly hover over "Chimps." This shouldn't close the "Gorilla Content"
 * area.
 *
 * This problem is normally solved using timeouts and delays. menu-aim tries to
 * solve this by detecting the direction of the user's mouse movement. This can
 * make for quicker transitions when navigating up and down the menu. The
 * experience is hopefully similar to amazon.com/'s "Shop by Department"
 * dropdown.
 *
 * Use like so:
 *
 *      $("#menu").menuAim({
 *          activate: $.noop,  // fired on row activation
 *          deactivate: $.noop  // fired on row deactivation
 *      });
 *
 *  ...to receive events when a menu's row has been purposefully (de)activated.
 *
 * The following options can be passed to menuAim. All functions execute with
 * the relevant row's HTML element as the execution context ('this'):
 *
 *      .menuAim({
 *          // Function to call when a row is purposefully activated. Use this
 *          // to show a submenu's content for the activated row.
 *          activate: function() {},
 *
 *          // Function to call when a row is deactivated.
 *          deactivate: function() {},
 *
 *          // Function to call when mouse enters a menu row. Entering a row
 *          // does not mean the row has been activated, as the user may be
 *          // mousing over to a submenu.
 *          enter: function() {},
 *
 *          // Function to call when mouse exits a menu row.
 *          exit: function() {},
 *
 *          // Selector for identifying which elements in the menu are rows
 *          // that can trigger the above events. Defaults to "> li".
 *          rowSelector: "> li",
 *
 *          // You may have some menu rows that aren't submenus and therefore
 *          // shouldn't ever need to "activate." If so, filter submenu rows w/
 *          // this selector. Defaults to "*" (all elements).
 *          submenuSelector: "*",
 *
 *          // Direction the submenu opens relative to the main menu. Can be
 *          // left, right, above, or below. Defaults to "right".
 *          submenuDirection: "right"
 *      });
 *
 * https://github.com/kamens/jQuery-menu-aim
 * MIT License
*/
!function(e){e.fn.menuAim=function(t){return this.each(function(){(function(t){var n=e(this),i=null,o=[],u=null,r=null,c=e.extend({rowSelector:"> li",submenuSelector:"*",submenuDirection:"right",tolerance:75,enter:e.noop,exit:e.noop,activate:e.noop,deactivate:e.noop,exitMenu:e.noop},t),l=function(e){e!=i&&(i&&c.deactivate(i),c.activate(e),i=e)},f=function(e){var t=a();t?r=setTimeout(function(){f(e)},t):l(e)},a=function(){if(!i||!e(i).is(c.submenuSelector))return 0;var t=n.offset(),r={x:t.left,y:t.top-c.tolerance},l={x:t.left+n.outerWidth(),y:r.y},f={x:t.left,y:t.top+n.outerHeight()+c.tolerance},a={x:t.left+n.outerWidth(),y:f.y},s=o[o.length-1],h=o[0];if(!s)return 0;if(h||(h=s),h.x<t.left||h.x>a.x||h.y<t.top||h.y>a.y)return 0;if(u&&s.x==u.x&&s.y==u.y)return 0;function m(e,t){return(t.y-e.y)/(t.x-e.x)}var x=l,y=a;"left"==c.submenuDirection?(x=f,y=r):"below"==c.submenuDirection?(x=a,y=f):"above"==c.submenuDirection&&(x=r,y=l);var v=m(s,x),p=m(s,y),b=m(h,x),d=m(h,y);return v<b&&p>d?(u=s,300):(u=null,0)};n.mouseleave(function(){r&&clearTimeout(r);c.exitMenu(this)&&(i&&c.deactivate(i),i=null)}).find(c.rowSelector).mouseenter(function(){r&&clearTimeout(r);c.enter(this),f(this)}).mouseleave(function(){c.exit(this)}).click(function(){l(this)}),e(document).mousemove(function(e){o.push({x:e.pageX,y:e.pageY}),o.length>3&&o.shift()})}).call(this,t)}),this}}(jQuery);