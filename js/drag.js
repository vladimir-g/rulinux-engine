var Dragger		= "var eve=arguments.length?arguments[0]:event;" +
			  "Drag.ox=eve.clientX-this.offsetLeft;" +
			  "Drag.oy=eve.clientY-this.offsetTop;"  +
			  "this.fire=Drag.fire;this.fire();false;";
var Drag = {
 ox : 0, oy : 0,
 minx : null, maxx : null, miny : null, maxy : null,
 mode	: 0,
 affine	: null,
 initer	: null,
 
 init	: function(node,mode,minx,miny,maxx,maxy) { 
  var retstr		= "with(Drag) mode="+mode+",minx="+minx+",maxx="+maxx
  			+ ",miny="+miny+",maxy="+maxy+";Drag.initer=1;"+Dragger;
  node.onmousedown	= new Function("e", "return eval(\""+retstr+"\")");
  return (Drag.initer=retstr);
 },
 add	: function(node) { node.out = Drag.out; node.out(null); },
 fire	: function() {
  var that		= this;
  that.run		= Drag.run;
  that.out		= Drag.out;
  that.style.position	= "absolute";
  that.onmousedown	= null;
  that.onmouseup	= function(e) {return that.out(e);};
  document.onmouseup	= function(e) {return that.out(e);};
  document.onmousemove	= function(e) {return that.run(e);};
  document.onmouseout	= function(e) {
   var eve		= e?e:event;
   if(!eve.fromElement) 
    eve.fromElement	= eve.target, eve.toElement = eve.relatedTarget;
   if(!eve.toElement) that.out(e);return false;
  }; return false;
 },
 run	: function(e) {
  var eve				= e?e:event;
  var nx				= eve.clientX-Drag.ox;
  var ny				= eve.clientY-Drag.oy;
  with(Drag) {
   if(minx) if(nx<minx) nx		= minx;
   if(maxx) if(nx>maxx) nx		= maxx;
   if(miny) if(ny<miny) ny		= miny;
   if(maxy) if(ny>maxy) ny		= maxy;
  } if(this.parentNode.style.position=="absolute") {
   nx = nx - this.parentNode.offsetLeft;
   ny = ny - this.parentNode.offsetTop;
  }
  if(Drag.mode<2) this.style.left	= nx+"px";
  if(!(Drag.mode%2)) this.style.top	= ny+"px";
  if(Drag.mode==3 && Drag.affine) 
   Drag.affine(this, nx, ny);
  return false;
 },

 out	: function(e) {
  var invoker		= null;
  document.onmousemove	= null;
  document.onmouseup	= null;
  document.onmouseout	= null;
  this.onmouseup	= null;
  with(Drag) { invoker= mode+","+minx+","+miny+","+maxx+","+maxy; }
  this.onmousedown	= new Function("e","return eval("
  			+ (Drag.initer?"Drag.init(this,"+invoker+")":"Dragger")+")");
  with(Drag) {mode=0,minx=null,maxx=null,miny=null,maxy=null;initer=null}
  return false;
 }
};
