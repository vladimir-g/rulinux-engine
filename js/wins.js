//Dimensional array with window indices.
var descriptors = new Array();
var pointer = -1;
descriptors[0]='NaN';

function maxminWin(hwnd){
	var getWin = document.getElementById('win'+hwnd);
	if (!descriptors[hwnd]['maximized']){
		getWin.style.height = window.outerHeight - 32
		getWin.style.width = "100%";
		getWin.style.top = 32;
		getWin.style.left = 0;
		descriptors[hwnd]['maximized'] = true;
	}
	else{
		getWin.style.height = descriptors[hwnd]['height']
		getWin.style.width = descriptors[hwnd]['width']
		getWin.style.top = descriptors[hwnd]['top'];
		getWin.style.left = descriptors[hwnd]['left'];
		descriptors[hwnd]['maximized'] = false;
	}
}

//Window reating  function
function addWin(x, y, width, height, title, content, foreGround, icon, stalone){
   var newWin = new String();
   var workFlow = document.getElementById('workflow');
	if (stalone)
		workFlow.innerHTML += '<div id="lowLayer" style="position:absolute; z-index: 0; top: 0px; left: 0px; height:100%; width:100%"></div>';
   //Array with attributes of new window (i.e.: top, left, height, width), which will be pushed into descriptors array
   var winDesc = new Array();
	if (x == 'center')
		x = (window.outerWidth/2) - (width/2);
	if (y == 'center')
		y = (window.outerHeight/2) - (height/2);
   newWin += '<div onmousedown="focusWin(\'' + (descriptors.length-1) + '\')" id="win'+(descriptors.length-1)+'" style="padding:0; margin:0; position:absolute; z-index: '+(descriptors.length-1)+'; border:2px solid #000000; background-color:#ffffff; width: '+width+'; height: '+height+'; top: '+(y+20)+'px; left: '+x+'px">';
   newWin += '<div ondblclick="maxminWin('+(descriptors.length-1)+')" id="title'+(descriptors.length-1)+'" onmouseover="captureWin('+(descriptors.length-1)+')" style="padding:0; margin:0; position:relative; border:none; background-color:#000000; background-image:url(design/admin/img/winbg.png); width: 100%; height: 20px; top: 0px; left: 0px">';
   newWin += '<table border="0" cellspacing="0" cellspading="0" style="padding:0; margin:0;" width="100%">';
   newWin += '<tr>';
   newWin += '<td style="vertical-align:top;adding:0; margin:0; width:18px;"><img src="'+icon+'"></td>';
   newWin += '<td style="color:#ffffff; vertical-align:top; text-align:center" id="tText'+(descriptors.length-1)+'">' + title + '</td>';
   newWin += '<td style="vertical-align:top;adding:0; margin:0; width:18px;">';
   newWin += '<img src="design/admin/img/closeWin.png" alt="Закрыть" title="Закрыть" border="0" onmouseover="this.src=\'design/admin/img/closeWin-hover.png\'" onmouseout="this.src=\'design/admin/img/closeWin.png\';"  onmousedown="this.src=\'design/admin/img/closeWin-md.png\'" onclick="destroyWin('+(descriptors.length-1)+')">';
   newWin += '</td>';
   newWin += '</tr>';
   newWin += '</table>';
   newWin += '</div>';
   newWin += '<div id="content'+(descriptors.length-1)+'" style="height: '+(height-20)+'px; overflow: auto;">'+content+'</div>';
   newWin += '</div>';
   winDesc['length'] = newWin.length;
   winDesc['x'] = x;
   winDesc['x'] = y;
   winDesc['width'] = width;
   winDesc['height'] = height;
	winDesc['maximized'] = false;
	winDesc['stalone'] = stalone;
   winDesc['title'] = title;
   descriptors.push(winDesc);
   workFlow.innerHTML += newWin;
   //returning index of new window
   return descriptors.length-2;
}

function sendMessage(hwnd, property, value){
	var success = false;
	var win = document.getElementById('win'+hwnd);
	
	switch (property){
		case 'x': win.style.left = value; success = true; break;
		case 'y': win.style.top = value; success = true; break;
		case 'height': win.height = value; success = true; break;
		case 'width': win.width = value; success = true; break;
		case 'content': document.getElementById('content'+hwnd).innerHTML = value; success = false; break;
		case 'title': document.getElementById('tText'+hwnd).innerHTML = value; success = true; break;
	}
	if (success)
		descriptors[hwnd][property] = value;
	return success;
}

//	destruction of the windows by its index
function destroyWin(hwnd){
   var getWin = document.getElementById('win'+hwnd);
   var getTitle = document.getElementById('title'+hwnd);
   var workFlow = document.getElementById('workflow');
	if (descriptors[hwnd+1]['stalone']){
		var lowLayer = document.getElementById('lowLayer');
		workFlow.removeChild(lowLayer);
	}
   getWin.innerHTML = '';
   workFlow.removeChild(getWin);
   workFlow.removeChild(getTitle);
   //Now remove this window from the descriptors array
   descriptors.splice(hwnd, 1);
}

function captureWin(hwnd){
   var getWin = document.getElementById('win'+hwnd);
   var getTitle = document.getElementById('title'+hwnd);
	Drag.init(getTitle, getWin);
}

function focusWin(hwnd){
   var getWin = document.getElementById('win'+hwnd);
   for (i = hwnd; i < descriptors.length; i++){
      if (document.getElementById('win'+i) != null){
         getWin = document.getElementById('win'+i);
         getWin.style.zIndex = i;
      }
   }
   getWin = document.getElementById('win'+hwnd);
   getWin.style.zIndex = descriptors.length-1;
}

/*
 *
 * This next part of script is written not by me,
 * great thanks to its autor :)
 * 
 */

var Drag = {

	obj : null,

	init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper)
	{
      minY = 32;
		o.onmousedown	= Drag.start;

		o.hmode			= bSwapHorzRef ? false : true ;
		o.vmode			= bSwapVertRef ? false : true ;

		o.root = oRoot && oRoot != null ? oRoot : o ;

		if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
		if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
		if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
		if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

		o.minX	= typeof minX != 'undefined' ? minX : null;
		o.minY	= typeof minY != 'undefined' ? minY : null;
		o.maxX	= typeof maxX != 'undefined' ? maxX : null;
		o.maxY	= typeof maxY != 'undefined' ? maxY : null;

		o.xMapper = fXMapper ? fXMapper : null;
		o.yMapper = fYMapper ? fYMapper : null;

		o.root.onDragStart	= new Function();
		o.root.onDragEnd	= new Function();
		o.root.onDrag		= new Function();
	},

	start : function(e)
	{
		var o = Drag.obj = this;
		e = Drag.fixE(e);
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		o.root.onDragStart(x, y);

		o.lastMouseX	= e.clientX;
		o.lastMouseY	= e.clientY;

		if (o.hmode) {
			if (o.minX != null)	o.minMouseX	= e.clientX - x + o.minX;
			if (o.maxX != null)	o.maxMouseX	= o.minMouseX + o.maxX - o.minX;
		} else {
			if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
			if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
		}

		if (o.vmode) {
			if (o.minY != null)	o.minMouseY	= e.clientY - y + o.minY;
			if (o.maxY != null)	o.maxMouseY	= o.minMouseY + o.maxY - o.minY;
		} else {
			if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
			if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
		}

		document.onmousemove	= Drag.drag;
		document.onmouseup		= Drag.end;

		return false;
	},

	drag : function(e)
	{
		e = Drag.fixE(e);
		var o = Drag.obj;

		var ey	= e.clientY;
		var ex	= e.clientX;
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		var nx, ny;

		if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
		if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
		if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
		if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

		nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
		ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

		if (o.xMapper)		nx = o.xMapper(y)
		else if (o.yMapper)	ny = o.yMapper(x)

		Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
		Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
		Drag.obj.lastMouseX	= ex;
		Drag.obj.lastMouseY	= ey;

		Drag.obj.root.onDrag(nx, ny);
		return false;
	},

	end : function()
	{
		document.onmousemove = null;
		document.onmouseup   = null;
		Drag.obj.root.onDragEnd(	parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), 
									parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
		Drag.obj = null;
	},

	fixE : function(e)
	{
		if (typeof e == 'undefined') e = window.event;
		if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
		if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
		return e;
	}
};