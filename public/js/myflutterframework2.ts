//Esta funcion se lanzara cuando se hayan creados o actualizados todos los widgets
function oncreatedOrUpdatedWidgetState(){
    // console.log("Termino terminoooooooooooooooooooooooooooooooooooooooooooooooooooooooooo");
    
    rebuildSizeFromLayoutBuilder();
    // $('.main-panel').perfectScrollbar('update');
}


/********************* VARIABLES  ************************************/

let fontSizeUnit : string = "px";
let heightUnit : string = "vh";
let widthUnit : string = "vw";

class ScreenSize{
    static xs:number = 567;
    static sm:number = 791;
    static md:number = 999;
    static lg:number = 1000;

    static isXs(number:number){
        return number >= 0 && number <= this.xs;
    }
    static isSm(number:number){
        return number >= (this.xs + 1) && number <= this.sm;
    }
    static isMd(number:number){
        return number >= (this.sm + 1) && number <= this.md;
    }
    static isLg(number:number){
        return number >= this.lg;
    }
}


/********************* FUNCTIONES  ************************************/

// function TextEditingController(){
//     let input = document.createElement("input");

// }

function ramdomString(length : number) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
       result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
 }

 interface namedParametersSetDeviceSize{
     xs?:number;
     sm?:number;
     md?:number;
     lg?:number;
     screenSize?:number;
 }

 function setDeviceSize({screenSize, xs, sm, md, lg} : namedParametersSetDeviceSize){
    let width:number = screenSize;
    console.log("setDeviceSize: ", width);
    
    if(ScreenSize.isXs(screenSize)){
        console.log("setDeviceSize xs: ", width);
        if(xs)
            width = screenSize / xs;
    }
    else if(ScreenSize.isSm(screenSize)){
        console.log("setDeviceSize sm: ", width);
        if(sm)
            width = screenSize / sm;
    }
    else if(ScreenSize.isMd(screenSize)){
        console.log("setDeviceSize md: ", width);
        if(md)
            width = screenSize / md;
    }
    else if(ScreenSize.isLg(screenSize)){
        console.log("setDeviceSize lg: ", width);
        if(lg)
            width = screenSize / lg;
    }
    
    return width;
 }

 function flexbox(element: HTMLDivElement){
    element.style.cssText += "display: -webkit-box";
    element.style.cssText += "display: -moz-box";
    element.style.cssText += "display: -webkit-flex";
    element.style.cssText += "display: -ms-flexbox";
    element.style.cssText += "display: flex";
    return element;
  }

 function justifyContentPrefix(element: HTMLDivElement, value: string = 'flex-start') {
    if(value == 'flex-start') {
      element.style.cssText += `-webkit-box-pack: start;`;
      element.style.cssText += `-moz-box-pack: start;`;
      element.style.cssText += `-ms-flex-pack: start;`;
    } else if(value == 'flex-end') {
      element.style.cssText += `-webkit-box-pack: end`;
      element.style.cssText += `-moz-box-pack: end;`;
      element.style.cssText += `-ms-flex-pack: end;`;
    } else if (value == 'space-between') {
      element.style.cssText += `-webkit-box-pack: justify`;
      element.style.cssText += `-moz-box-pack: justify;`;
      element.style.cssText += `-ms-flex-pack: justify;`;
    } else if (value == 'space-around') {
      element.style.cssText += `-ms-flex-pack: distribute;`;
    } else {
      element.style.cssText += `-webkit-box-pack: ${value}`;
      element.style.cssText += `-moz-box-pack: ${value};`;
      element.style.cssText += `-ms-flex-pack: ${value};`;
    }
    element.style.cssText += `-webkit-justify-content: ${value}`;
    element.style.cssText += `justify-content: ${value};`;
    return element;
  }

  function alignItemsPrefix(element: HTMLDivElement, value: string = 'stretch') {
    if(value == "flex-start") {
        element.style.cssText += `-webkit-box-align: start;`;
        element.style.cssText += `-moz-box-align: start;`;
        element.style.cssText += `-ms-flex-align: start;`;
    } else if (value == "flex-end") {
        element.style.cssText += `-webkit-box-align: end;`;
        element.style.cssText += `-moz-box-align: end;`;
        element.style.cssText += `-ms-flex-align: end;`;
    } else {
        element.style.cssText += `-webkit-box-align: ${value};`;
        element.style.cssText += `-moz-box-align: ${value};`;
        element.style.cssText += `-ms-flex-align: ${value};`;
    }
    element.style.cssText += `-webkit-align-items: ${value};`;
    element.style.cssText += `align-items: ${value};`;
    return element;
  }

  function flexDirection(element: HTMLDivElement, value:string = "row") {
    if (value == "row-reverse") {
        element.style.cssText += `-webkit-box-direction: reverse`;
        element.style.cssText += `-webkit-box-orient: horizontal`;
        element.style.cssText += `-moz-box-direction: reverse`;
        element.style.cssText += `-moz-box-orient: horizontal`;
    } else if (value == "column") {
        element.style.cssText += `-webkit-box-direction: normal`;
        element.style.cssText += `-webkit-box-orient: vertical`;
        element.style.cssText += `-moz-box-direction: normal`;
        element.style.cssText += `-moz-box-orient: vertical`;
    } else if (value == "column-reverse") {
        element.style.cssText += `-webkit-box-direction: reverse`;
        element.style.cssText += `-webkit-box-orient: vertical`;
        element.style.cssText += `-moz-box-direction: reverse`;
        element.style.cssText += `-moz-box-orient: vertical`;
    } else {
        element.style.cssText += `-webkit-box-direction: normal`;
        element.style.cssText += `-webkit-box-orient: horizontal`;
        element.style.cssText += `-moz-box-direction: normal`;
        element.style.cssText += `-moz-box-orient: horizontal`;
    }
    element.style.cssText += `-webkit-flex-direction: ${value}`;
    element.style.cssText += `-ms-flex-direction: ${value}`;
    element.style.cssText += `flex-direction: ${value}`;
    return element;
  }

  function alignSelf(element:HTMLElement ,value:string = "auto") {
    // No Webkit Box Fallback.
    let cssText:string;
    cssText = `-webkit-align-self: ${value}`;
    if (value == 'flex-start') {
        cssText+= `-ms-flex-item-align: start`;
    } else if (value == 'flex-end') {
      cssText+= '-ms-flex-item-align: end';
    } else {
        cssText+= `-ms-flex-item-align: ${value}`;
    }
    cssText+= `align-self: ${value}`;
    return cssText;
  }


  function flexGrowPrefix(document:HTMLDivElement ,int: number = 1) {
    document.style.cssText += `-webkit-box-flex: ${int};`;
    document.style.cssText += `-moz-box-flex: ${int};`;
    document.style.cssText += `-webkit-flex-grow: ${int};`;
    document.style.cssText += `-ms-flex: ${int};;`
    document.style.cssText +=` flex-grow: ${int};`;
    return document;
  }


class TextEditingController{
    public input : any;
    private value : string;
    // public text : string;
    constructor(){
        this.text = "";
        this.value = "";
        this.input = document.createElement("input");
        this.input.addEventListener("input", () => {
            // console.log("Dentro del controller");
            this.text = this.input.value;
        })
    }

    set text(val:string){
        this.value = val;
    }

    get text(){
        return this.value;
    }
}

class FormGlobalKey{
    public form : any;
    public text : string;
    private error : boolean = false;
    // private callBackError = function(error : string){
    //     this.
    //     throw "Error FormGlobalKey";
    // }
    constructor(){
        this.text = "";
        this.form = document.createElement("form");
    }

    validate(){
        try {
            //Obtenemos todos los inputs que estan dentro de este form 'FormGlobalKey'
            var inputs = this.form.getElementsByTagName("input");
            
            var valid = true;
            //Callback que se llamara desde los inputs en caso de no ser validor
            var callBackError = (function(errorMessage : string){
                valid = false;
                throw "Error FormGlobalKey";
            }).bind(valid);
            //Creamos el evento personalizado 'CustomEvent' y le pasamos el callbackError
            var event = new CustomEvent('validate', {"detail" : callBackError});
            // Disparar event.
            // elem.dispatchEvent(event);
            //Disparamos cada uno de los eventos de cada input
            for(var i=0; i < inputs.length; i++){
                inputs[i].dispatchEvent(event);
            }

            return valid;
        } catch (error) {
            // console.log("Error FormGlobaKey validate");
            return false;
        }
    }
}




/********************* INTERFACES  ************************************/




/********************* STYLES  ************************************/
function InitDefaultStyle(){
    
      
      

    var buttonHover = 'div.buttonHover:hover{ background-color: rgba(0,0,0,0.8); filter:brightness(0.9); }';
    buttonHover += 'div.buttonFlatHover:hover{ background-color: rgba(0,0,0,0.05); filter:brightness(0.9); }'
    // var flex = '.flex {display: -webkit-box;display: -moz-box;display: -webkit-flex;display: -ms-flexbox;display: flex;}'
    // var row = '.row {-webkit-box-direction: normal; -webkit-box-orient: horizontal;-moz-box-direction: normal;-moz-box-orient: horizontal;}'
    var labelFloating = 'label.labelFloating {color: #bdb9b9; position: absolute; transform-origin: top left; transform: translate(0, 14px) scale(1);transition: all .19s ease-in-out;}'
                        + 'label.labelFloating.active {transform: translate(0, -4px) scale(.70);}';
    var inputWithFloatingLabel = '.inputFloating {width: 100%; border:none; border-bottom: 0.01px solid #c7c5c5; padding: 10px 0px; outline: none;}';
    var scrollbar = '::-webkit-scrollbar {width: 6px;}';
    scrollbar += '::-webkit-scrollbar-track {background: #f6f6f6; }'; //scrollbar track
    scrollbar += '::-webkit-scrollbar-thumb {background: #c1c1c1; }'; //scrollbar thumb
    scrollbar += '::-webkit-scrollbar-thumb:hover {background: #555; }'; //scrollbar hover
    var dropdownItem = '.dropdownItem:hover{background: #f1f1f1;}'
    var resultarFilasImpares = 'tr.resultarFilasImpares:nth-child(even){background-color: #f2f2f2}'
    var cardTranslate3D = '.cardTranslate3D{transform: translate3d(-8px, 0px, 0px); all 0.5s cubic-bezier(0.29, 1.42, 0.79, 1) 0s;}'
    var cardTop = '.cardTop{transform: translate3d(10px, 0px, 0px); all 0.5s cubic-bezier(0.29, 1.42, 0.79, 1) 0s;}'
    var defaultStyle = document.getElementById("defaultStyle");
    if(defaultStyle == null || defaultStyle == undefined){
        var style = document.createElement('style');
        style.setAttribute("id", "defaultStyle");
        style.appendChild(document.createTextNode(buttonHover));
        style.appendChild(document.createTextNode(labelFloating));
        style.appendChild(document.createTextNode(inputWithFloatingLabel));
        style.appendChild(document.createTextNode(scrollbar));
        style.appendChild(document.createTextNode(dropdownItem));
        style.appendChild(document.createTextNode(resultarFilasImpares));
        style.appendChild(document.createTextNode(cardTranslate3D));
        style.appendChild(document.createTextNode(cardTop));
        // style.appendChild(document.createTextNode(flex));
        // style.appendChild(document.createTextNode(row));
        
        document.getElementsByTagName('head')[0].appendChild(style);
    }
}

class FontWeight {
    values : string[] = ["100", "200", "300", "400", "500", "600", "700", "800", "900", "bolder", "lighter", "normal", "bold"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }

    
    static w100 = new FontWeight(1);
    static w200 = new FontWeight(2);
    static w300 = new FontWeight(3);
    static w400 = new FontWeight(4);
    static w500 = new FontWeight(5);
    static w600 = new FontWeight(6);
    static w700 = new FontWeight(7);
    static w800 = new FontWeight(8);
    static w900 = new FontWeight(9);
    static bolder = new FontWeight(10);
    static lighter = new FontWeight(11);
    static normal = new FontWeight(12);
    static bold = new FontWeight(13);
    
    public toString = () : string => {
        return this.values[this.index];
    }
}

interface namedParametersBorderRadius {
    top? : number;
    right? : number;
    bottom? : number;
    left? : number;
}

class BorderRadius {
    top? : number;
    right? : number;
    bottom? : number;
    left? : number;

    private constructor({top, right, bottom, left} : namedParametersBorderRadius){
        this.top = top;
        this.right = right;
        this.bottom = bottom;
        this.left = left;
    }

    
    static all = (radius : number) => {
        return new BorderRadius({top : radius, right : radius, bottom : radius, left : radius})
    };
    static only = ({top, right, bottom, left} : namedParametersBorderRadius) => {
        return new BorderRadius({top, right, bottom, left});
    };
   
    
    public toString = () : string => {
        this.top = (this.top != null) ? this.top : 0;
        this.right = (this.right != null) ? this.right : 0;
        this.bottom = (this.bottom != null) ? this.bottom : 0;
        this.left = (this.left != null) ? this.left : 0;
        return `${this.top}${fontSizeUnit} ${this.right}${fontSizeUnit} ${this.bottom}${fontSizeUnit} ${this.left}${fontSizeUnit}`;
    }
}


class BorderStyle {
    values : string[] = ["solid", "dashed", "dotted", "double", "groove", "ridge", "none", "800", "900", "bolder", "lighter", "normal", "bold"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }

    
    static solid = new BorderStyle(1);
    static dashed = new BorderStyle(2);
    static dotted = new BorderStyle(3);
    static double = new BorderStyle(4);
    static groove = new BorderStyle(5);
    static ridge = new BorderStyle(6);
    static none = new BorderStyle(7);
    
    
    public toString = () : string => {
        return this.values[this.index];
    }
}

interface namedParametersBorderSide{
    color?:string;
    width?:number;
    style?:BorderStyle;
}

class BorderSide {
    color? : string;
    width? : number;
    style? : BorderStyle;

    constructor({color = "black", width = 0, style = BorderStyle.none} : namedParametersBorderSide){
        this.color = color;
        this.width = width;
        this.style = style;
    }

    
    public toString = () : string => {
        
        return `${this.width}${fontSizeUnit} ${this.style.toString()} ${this.color}`;
    }
}

interface namedParametersBorder{
    top? : BorderSide;
    right? : BorderSide;
    bottom? : BorderSide;
    left? : BorderSide;
}

interface namedParametersBorderAll{
    width?:number;
    color?:string;
    style?:BorderStyle;
}

class Border {
    top? : string;
    right? : string;
    bottom? : string;
    left? : string;

    private constructor({top, right, bottom, left} : namedParametersBorder){
        this.top = top.toString();
        this.right = right.toString();
        this.bottom = bottom.toString();
        this.left = left.toString();
    }

    
    static all = ({width = 1, color = "black", style = BorderStyle.solid} : namedParametersBorderAll) => {
        return new Border({top : new BorderSide({width, color, style}), right : new BorderSide({width, color, style}), bottom : new BorderSide({width, color, style}), left : new BorderSide({width, color, style})})
    };
    // static only = ({top, right, bottom, left} : namedParametersBorder) => {
    //     return new Border({top, right, bottom, left});
    // };
   
    
    // public toString = () : string => {
    //     this.top = (this.top != null) ? this.top : new BorderSide({});
    //     this.right = (this.right != null) ? this.right : new BorderSide({});
    //     this.bottom = (this.bottom != null) ? this.bottom : new BorderSide({});
    //     this.left = (this.left != null) ? this.left : new BorderSide({});
    //     return `${this.top}${fontSizeUnit} ${this.right}${fontSizeUnit} ${this.bottom}${fontSizeUnit} ${this.left}${fontSizeUnit}`;
    // }
}

interface namedParametersEdgeInsets {
    top? : number;
    right? : number;
    bottom? : number;
    left? : number;
}

class EdgetInsets {
    top? : number;
    right? : number;
    bottom? : number;
    left? : number;

    private constructor({top, right, bottom, left} : namedParametersEdgeInsets){
        this.top = top;
        this.right = right;
        this.bottom = bottom;
        this.left = left;
    }

    
    static all = (radius : number) => {
        return new EdgetInsets({top : radius, right : radius, bottom : radius, left : radius})
    };
    static only = ({top, right, bottom, left} : namedParametersBorderRadius) => {
        return new EdgetInsets({top, right, bottom, left});
    };
   
    
    public toString = () : string => {
        this.top = (this.top != null) ? this.top : 0;
        this.right = (this.right != null) ? this.right : 0;
        this.bottom = (this.bottom != null) ? this.bottom : 0;
        this.left = (this.left != null) ? this.left : 0;
        return `${this.top}${fontSizeUnit} ${this.right}${fontSizeUnit} ${this.bottom}${fontSizeUnit} ${this.left}${fontSizeUnit}`;
    }
}

class TextAlign {
    values : string[] = ["left", "right", "center", "justify"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }
    
    
    static left = new TextAlign(1);
    static right = new TextAlign(2);
    static center = new TextAlign(3);
    static justify = new TextAlign(4);

    
    public toString = () : string => {
        return this.values[this.index];
    }
}

class Alignment {
    values : string[] = ["left: 0;", "right: 0;"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }
    
    
    static start = new Alignment(1);
    static end = new Alignment(2);
    // static center = new Alignment(3);
    // static justify = new Alignment(4);

    
    public toString = () : string => {
        return this.values[this.index];
    }
}

class Icons {
    values : string[] = ["arrow_drop_down", "done", "center", "justify"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }
    
    
    static arrow_drop_down = new Icons(1);
    static done = new Icons(2);
    

    
    public toString = () : string => {
        return this.values[this.index];
    }
}

class MainAxisAlignment {
    values : string[] = ["flex-start", "flex-end", "center", "space-evenly", "space-around", "space-between"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }
    
    
    static start = new MainAxisAlignment(1);
    static end = new MainAxisAlignment(2);
    static center = new MainAxisAlignment(3);
    static spaceEvenly = new MainAxisAlignment(4);
    static spaceAround = new MainAxisAlignment(5);
    static spaceBetween = new MainAxisAlignment(6);
    
    public toString = () : string => {
        return this.values[this.index];
    }
}

class CrossAxisAlignment {
    values : string[] = ["flex-start", "flex-end", "center",  "stretch"];
    index : number;
    private constructor(index : number){
        this.index = index - 1;
    }
    
    
    static start = new CrossAxisAlignment(1);
    static end = new CrossAxisAlignment(2);
    static center = new CrossAxisAlignment(3);
    static stretch = new CrossAxisAlignment(4);

    
    public toString = () : string => {
        return this.values[this.index];
    }
}

interface namedParametersTextStyle {
    fontSize? : number; 
    fontWeight? : FontWeight; 
    textAlign? : TextAlign; 
    fontStyle? : string;
    color? : string;
    background? : string;
    padding? : EdgetInsets;
    margin? : EdgetInsets;
    borderRadius? : BorderRadius;
    width? : number;
    height? : number;
    fontFamily? : string;
    cursor? : string;
    border? : Border;
    borderTop? : string;
    borderBottom? : string;
    borderRight? : string;
    borderLeft? : string;

}

class TextStyle{
    fontSize? : number;
    fontWeight?: FontWeight;
    textAlign?: TextAlign;
    fontStyle?: string;
    color? : string;
    background? : string;
    borderRadius? : BorderRadius;
    padding? : EdgetInsets;
    margin? : EdgetInsets;
    width? : number;
    height? : number;
    fontFamily? : string;
    cursor? : string;
    borderTop? : string;
    borderBottom? : string;
    borderRight? : string;
    borderLeft? : string;

    constructor({fontSize, fontWeight, textAlign, fontStyle, color, background, padding, margin, borderRadius, width, height, fontFamily, cursor, border}: namedParametersTextStyle){
        this.fontSize = fontSize;
        this.fontWeight = fontWeight;
        this.textAlign = textAlign;
        this.fontStyle = fontStyle;
        this.color = color;
        this.background = background;
        this.borderRadius = borderRadius;
        this.width = width;
        this.height = height;
        this.padding = padding;
        this.margin = margin;
        this.fontFamily = fontFamily;
        this.cursor = cursor;
        if(border){
            if(border.top)
                this.borderTop = border.top;
            if(border.right)
                this.borderRight = border.right;
            if(border.bottom)
                this.borderBottom = border.bottom;
            if(border.left)
                this.borderLeft = border.left;
        }
        
        
    }

    toJson(){
        let jsonWithValuesNotNullToReturn : any = {}
        // Recorrer todas las propiedades de la case ignorando las que esta nulas 
        // para asi retornar los estilos que tengan valor
        for( var i in this){
            if (this.hasOwnProperty(i)) {
                if(this[i] != null && this[i] != undefined){
                    if(typeof this[i] == "object")
                        jsonWithValuesNotNullToReturn[i] = this[i].toString();
                    else{
                        let valor : any = "";
                        if(i == "fontSize" || i == "width" || i == "height"){
                            
                            valor += `${this[i]}${fontSizeUnit}`;
                            // console.log(`${valor}`);
                        }
                        else
                            valor = this[i];
                        jsonWithValuesNotNullToReturn[i] = valor;
                    }
                }
                    
                // console.log(i + " -> " + this[i]);
            }
        }

        

        return jsonWithValuesNotNullToReturn;
    }
}

interface namedParametersInputDecoration{
    labelText?: string;
    activeColor?: string;
}
class InputDecoration{
    labelText? : string;
    activeColor?: string;
    

    constructor({labelText, activeColor}: namedParametersInputDecoration){
        this.labelText = labelText;
        this.activeColor = activeColor;
    }

    toJson(){
        let jsonWithValuesNotNullToReturn : any = {}
        // Recorrer todas las propiedades de la case ignorando las que esta nulas 
        // para asi retornar los estilos que tengan valor
        for( var i in this){
            if (this.hasOwnProperty(i)) {
                if(this[i] != null && this[i] != undefined){
                    if(typeof this[i] == "object")
                        jsonWithValuesNotNullToReturn[i] = this[i].toString();
                    else{
                        let valor : any = "";
                        if(i == "fontSize" || i == "width" || i == "height"){
                            
                            valor += `${this[i]}${fontSizeUnit}`;
                            // console.log(`${valor}`);
                        }
                        else
                            valor = this[i];
                        jsonWithValuesNotNullToReturn[i] = valor;
                    }
                }
                    
                // console.log(i + " -> " + this[i]);
            }
        }

        

        return jsonWithValuesNotNullToReturn;
    }
}



class ContainerStyle{
    color : number;
    fontWeight: FontWeight;
    textAlign: TextAlign;
    fontStyle?: string;

    constructor(color : number, fontWeight : FontWeight, textAlign : TextAlign, fontStyle? : string){
        this.color = color;
        this.fontWeight = fontWeight;
        this.textAlign = textAlign;
        this.fontStyle = fontStyle;
    }

    public toJson(){
        let jsonWithValuesNotNullToReturn : any = {}
        // Recorrer todas las propiedades de la case ignorando las que esta nulas 
        // para asi retornar los estilos que tengan valor
        for( var i in this){
            if (this.hasOwnProperty(i)) {
                if(this[i] != null)
                    jsonWithValuesNotNullToReturn[i] = this[i];
                // console.log(i + " -> " + this[i]);
            }
        }

        return jsonWithValuesNotNullToReturn;
    }
}


/********************* WIDGETS  ************************************/


interface namedParametersInit{
    id : string;
    child : any;
    style? : TextStyle;
    initDefaultStyle? : boolean;
}
function Init({id, child, style, initDefaultStyle = false} : namedParametersInit){
    var element : any = document.getElementById(id);
    if(style != null && style != undefined){
        // console.log("Container style: ", style);
        var styleJson = style.toJson();
        Object.keys(styleJson).forEach(key => {
            element.style[key] = styleJson[key];
        });
    }

    if(initDefaultStyle){
        InitDefaultStyle();
    }

    return {"element" : element, "child" : [child]};
}

// class Init{
//     id: string;
//     child: any;
//     style?: TextStyle;
//     initDefaultStyle?: boolean; 
//     readonly runType = "Init"; 
//     constructor({id, child, style, initDefaultStyle = false} : namedParametersInit){
//         this.id = id;
//         this.child = child;
//         this.style = style;
//         this.initDefaultStyle = initDefaultStyle;
//     }

//     toJson(){
//         var element : any = document.getElementById(this.id);
//         if(this.style != null && this.style != undefined){
//             console.log("Container style: ", this.style);
//             var styleJson = this.style.toJson();
//             Object.keys(styleJson).forEach(key => {
//                 element.style[key] = styleJson[key];
//             });
//         }
    
//         if(this.initDefaultStyle){
//             InitDefaultStyle();
//         }
    
//         return {"element" : element, "child" : this.child};
//     }
// }

interface Object {
    // ...
  
    /** Returns a string representation of an object. */
    toString(): string;
  
    /** Returns a date converted to a string using the current locale. */
    toLocaleString(): string;
  
    /** Returns the primitive value of the specified object. */
    valueOf(): Object;
  
    /**
     * Determines whether an object has a property with the specified name.
     * @param v A property name.
     */
    hasOwnProperty(v: string): boolean;
  
    /**
     * Determines whether an object exists in another object's prototype chain.
     * @param v Another object whose prototype chain is to be checked.
     */
    isPrototypeOf(v: Object): boolean;
  
    /**
     * Determines whether a specified property is enumerable.
     * @param v A property name.
     */
    propertyIsEnumerable(v: string): boolean;
  }

interface Widget{
    child: any[],
    element: any,
    style: any,
    type: string,
    isStateLess?:boolean;
}

function Texto(text : string, style : TextStyle = new TextStyle({}), isStateLess = false) : Widget{
    var element = document.createElement("p");
    element.setAttribute("id", 'Texto-' +  ramdomString(7));
    element.innerHTML = text;
    element.style.padding = "0px";
    element.style.margin = "0px";
    
    element.classList.add("cambios");
    element.addEventListener("cambios", (function(){
        // console.log("Cambios en el dom text: ", text);
    }).bind(text))

    // Object.keys(style.toJson()).forEach(key => {
    //     element.style[key] = style[key];
    // });
    
    var styleJson = style.toJson();
    // console.log("TExt style json: ", styleJson);
    Object.keys(styleJson).forEach(key => {
        element.style[key] = styleJson[key];
        // console.log("TExt style foreach: ", styleJson[key]);
    });
    
    
    return  {element : element, style : styleJson, type : "Texto", child : [], isStateLess: isStateLess} ;
}

interface namedParametersTexto{
    text: string;
    style?: TextStyle;
    id?: string;
}

// class Texto{
//     text: string;
//     style?: TextStyle;
//     id?: string;
//     readonly runType = "Texto"; 
//     constructor({text, style, id}: namedParametersTexto){
//         this.text = text;
//         this.style = style;
//         this.id = id;
//     }

//     toJson(){
//         var element = document.createElement("p");
//         element.setAttribute("id", ramdomString(5));
//         element.innerHTML = this.text;
//         element.style.padding = "0px";
//         element.style.margin = "0px";
        
//         element.classList.add("cambios");
//         element.addEventListener("cambios", (function(){
//             // console.log("Cambios en el dom text: ", this.text);
//         }).bind(this.text))
    
//         // Object.keys(style.toJson()).forEach(key => {
//         //     element.style[key] = style[key];
//         // });
        
//         if(this.style != null && this.style != undefined){
//             var styleJson = this.style.toJson();
//             console.log("TExt style json: ", styleJson);
//             Object.keys(styleJson).forEach(key => {
//                 element.style[key] = styleJson[key];
//                 console.log("TExt style foreach: ", styleJson[key]);
//             });
//         }
        
//         return {"element" : element, "child" : null};
//     }
// }

interface namedParametersContainer {
    child? : any;
    style? : TextStyle;
    id? : string;
}


function Container({child, style, id}: namedParametersContainer){
    var element = document.createElement("div");
    if(id == null || id == undefined)
        element.setAttribute("id", 'Container-' + ramdomString(7));
    else
        element.setAttribute("id", id + "-" + ramdomString(7));
    
    // Object.keys(style.toJson()).forEach(key => {
    //     element.style[key] = style[key];
    // });
    var styleJson = {};
    if(style != null && style != undefined){
        // console.log("Container style: ", style);
        styleJson = style.toJson();
        Object.keys(styleJson).forEach(key => {
            element.style[key] = styleJson[key];
        });
    }
    
    if(child != null && child != undefined)
        return {"element" : element, "style" : styleJson, "type" : "Container", "child" : [child]};
    else
        return {"element" : element, "style" : styleJson, "type" : "Container", "child" : []};
}

interface namedParametersCardTranslate3D{
    color?:string;
    text:string;
}


function CardTranslate3D({text, color = "#00bcd4"}: namedParametersCardTranslate3D){
    var element = document.createElement("div");
    element.setAttribute("id", 'CardTranslate3D-' + ramdomString(7));
    element.classList.add("cardTranslate3D");
    element.style.cssText = `width: 100%; padding: 12px; border-radius: 4px; background: ${color}; box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0, 188, 212, .4);`;
    let child:any = Texto(text, new TextStyle({textAlign: TextAlign.center, color: "white", fontSize: 12, fontWeight: FontWeight.w700}));
    element.animate([
        // keyframes
        // { transform: 'translateY(0px)' }, 
        // { transform: 'translateY(50px)' }
        { transform: 'translate3d(-8px, 0px, 0px);' }, 
        // { transform: 'scale(1)' }
        ], 
        { 
            // timing options
            duration: 100,
            // iterations: Infinity
        }
    );
    
    // Object.keys(style.toJson()).forEach(key => {
    //     element.style[key] = style[key];
    // });
    
    
    if(child != null && child != undefined)
        return {"element" : element, "style" : "", "type" : "Container", "child" : [child]};
    else
        return {"element" : element, "style" : "", "type" : "Container", "child" : []};
}

function CardTop({text, color = "#00bcd4"}: namedParametersCardTranslate3D){
    var element = document.createElement("div");
    element.setAttribute("id", 'CardTop-' + ramdomString(7));
    element.classList.add("cardTop");
    element.style.cssText = `display: inline; margin-top: -15px; padding: 18px 10px; border-radius: 4px; background: ${color}; box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0, 188, 212, .4);`;
    let child:any = Texto(text, new TextStyle({textAlign: TextAlign.center, color: "white", fontSize: 17, fontWeight: FontWeight.w300}));
   
    
    // Object.keys(style.toJson()).forEach(key => {
    //     element.style[key] = style[key];
    // });
    
    
    if(child != null && child != undefined)
        return {"element" : element, "style" : "", "type" : "Container", "child" : [child]};
    else
        return {"element" : element, "style" : "", "type" : "Container", "child" : []};
}

// interface namedParametersDataRow{
//     cells:[]
// }


interface namedParametersDataTable{
    columns:DataColumn[];
    rows:DataRow[];
    resultarFilasImpares?:boolean;
}

function DataTable({columns, rows, resultarFilasImpares = false}:namedParametersDataTable){
    
    
    let element = document.createElement("table");
    element.setAttribute("id", "DataTable-" + ramdomString(7));
    element.style.cssText = "width: 95%; margin: 0px auto;";
    let head = document.createElement("thead");
    head.setAttribute("id", "DataTableHead-" + ramdomString(7));
    let body = document.createElement("tbody");
    body.setAttribute("id", "DataTableBody-" + ramdomString(7));
    let headRow = document.createElement("tr");
    body.setAttribute("id", "DataTableHeadRow-" + ramdomString(7));
    let children:any[] = [];

    let headRowElementToWidget:any = {
        "element" :headRow,
        "child" : columns
    }
    let headElementToWidget:any = {
        "element" :head,
        "child" : [
            headRowElementToWidget,
        ]
    }
    var bodyRow = document.createElement("tr");
    
    var rowsToWidget:any[] = [];
    for(let i of rows){
        let row:any = i.toJson();
        if(resultarFilasImpares)
            row.element.classList.add("resultarFilasImpares");
        
        rowsToWidget.push(row)
    }
    let bodyElementToWidget:any = {
        "element" :body,
        "child" : rowsToWidget
    }

    children.push(headElementToWidget);
    children.push(bodyElementToWidget);

   
    
    
    
    // for(let i of columns){
    //     let columnWidget:any = i.toJson();
    //     let columnElement = columnWidget.element; 
    //     let columnChild = columnWidget.child[0].element; 

    //     columnElement.appendChild(columnChild);
    //     headRow.appendChild(columnElement)
    // }
    // head.appendChild(headRow);

    // var bodyRow = document.createElement("tr");
    // for(let i of rows){
    //     body.appendChild(i.toJson().child[0])
    // }
    // element.appendChild(head);

    if(children != null && children != undefined)
        {
            console.log("Dentro iffff");
            return {"element" : element, "style" : "", "type" : "Container", "child" : children};
        }
    else
        {
            console.log("Dentro elllllllseeee");
            
            return {"element" : element, "style" : "", "type" : "Container", "child" : []}
        };
    
}


interface namedParametersDataRow{
    cells:DataCell[];
    onSelectedChanged?:any;
}

class DataRow{
    element:HTMLTableRowElement;
    cells:DataCell[];
    child:any[] = [];
    constructor({cells, onSelectedChanged}:namedParametersDataRow){
        this.element = document.createElement("tr");
        this.element.setAttribute("id", "DataRow-" + ramdomString(7));
        if(onSelectedChanged){
            this.element.addEventListener("click", onSelectedChanged);
            this.element.classList.add("buttonFlatHover");
            this.element.style.cursor = "pointer";
        }
        this.cells = cells;
    }
    
    toJson(){
        for(var i of this.cells){
            this.child.push(i.toJson())
        }
        if(this.child != null && this.child != undefined)
        return {"element" : this.element, "style" : "", "type" : "Container", "child" : this.child};
        else
            return {"element" : this.element, "style" : "", "type" : "Container", "child" : []};
    }
    
}


class DataCell{
    element:HTMLTableDataCellElement;
    child:any;
    private constructor(child:any){
        this.element = document.createElement("td");
        // this.element.setAttribute("scope", "col")
        this.element.setAttribute("id", "DataCell-" + ramdomString(7));
        this.element.style.cssText += "padding: 10px; 0px;";

        this.child = child;
    }
    
    toJson(){
        if(this.child != null && this.child != undefined)
        return {"element" : this.element, "style" : "", "type" : "Container", "child" : [this.child]};
        else
            return {"element" : this.element, "style" : "", "type" : "Container", "child" : []};
    }
    
}

interface namedParametersDataColumn{
    label? : any;
}

class DataColumn{
    element:HTMLTableDataCellElement;
    child:any;
    constructor({label}:namedParametersDataColumn){
        this.element = document.createElement("td");
        this.element.setAttribute("scope", "col")
        this.element.setAttribute("id", "DataColumn-" + ramdomString(7));
        this.element.style.cssText = "border-bottom: 0.1px solid #c1c1c1; padding-bottom: 10px;";
        this.child = label;
    }
    
    public toJson(){
        if(this.child != null && this.child != undefined)
        return {"element" : this.element, "style" : "", "type" : "Container", "child" : [this.child]};
        else
            return {"element" : this.element, "style" : "", "type" : "Container", "child" : []};
    }
    
}



// class Container{
//     child : any;
//     style? : TextStyle;
//     id? : string;
//     readonly runType = "Container"; 
//     constructor({child, style, id}: namedParametersContainer){
//         this.child = child;
//         this.style = style;
//         this.id = id;
//     }

    

//     toJson(){
//         var element = document.createElement("div");
//         element.setAttribute("id", ramdomString(5));
        
//         // Object.keys(style.toJson()).forEach(key => {
//         //     element.style[key] = style[key];
//         // });
//         if(this.style != null && this.style != undefined){
//             console.log("Container style: ", this.style);
//             var styleJson = this.style.toJson();
//             Object.keys(styleJson).forEach(key => {
//                 element.style[key] = styleJson[key];
//             });
//         }
        
//         return {"element" : element, "child" : this.child};
//     }
// }


interface namedParametersRow {
    children : any[];
    mainAxisAlignment? : MainAxisAlignment;
    crossAxisAlignment? : CrossAxisAlignment;
    id?:string;
}


function Row({children, mainAxisAlignment, crossAxisAlignment, id} : namedParametersRow){
    var element = document.createElement("div");
    if(id == null || id == undefined)
        element.setAttribute("id", "Row-" + ramdomString(7));
    else
        element.setAttribute("id", `${id}-` + ramdomString(7));

    //Display: flexbox; prefix
    
    element.setAttribute("style", "-webkit-flex-flow: row nowrap;-ms-flex-flow: row nowrap;flex-flow: row nowrap;")
    element.setAttribute("style", 'display: -webkit-box;display: -moz-box;display: -webkit-flex;display: -ms-flexbox;display: flex;');
    element.style.width = "100%";
    //flexDirection: row; prefix
    // element.setAttribute("style", '-webkit-box-direction: normal; -webkit-box-orient: horizontal;-moz-box-direction: normal;-moz-box-orient: horizontal; -webkit-flex-direction: row; -ms-flex-direction: row; flex-direction: row;');
    
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    let styleJson : any = {};
    // element.style.display = "flex";
    // element.style.flexDirection = "row";
    // element.classList.add("flex");
    // element.classList.add("row");
    if(mainAxisAlignment != null && mainAxisAlignment != undefined){
        // let mainAxis: string = mainAxisAlignment.toString();
        // let mainAxisWithoutFlexString: string = mainAxis.replace("flex-", "");
        // console.log("Row mainAxis: ", mainAxis);
        // element.setAttribute("style", `-webkit-box-pack: ${mainAxisWithoutFlexString}; -moz-box-pack: ${mainAxisWithoutFlexString}; -ms-flex-pack: ${mainAxisWithoutFlexString}; -webkit-justify-content: ${mainAxis}; justify-content: ${mainAxis};`);
        // element.style.justifyContent = mainAxisAlignment.toString();
        // styleJson.justifyContent = mainAxisAlignment.toString(); 
        element = justifyContentPrefix(element, mainAxisAlignment.toString());
        // console.log("Row mainAxisAligment: ", element.style.cssText);
        
    }
    if(crossAxisAlignment != null && crossAxisAlignment != undefined){
        // element.style.alignItems = crossAxisAlignment.toString();
        // styleJson.alignItems = crossAxisAlignment.toString(); 
        element = alignItemsPrefix(element, crossAxisAlignment.toString())
    }

    return {"element" : element, "style" : styleJson, "type" : "Row", "child" : children};
}

function Column({children, mainAxisAlignment, crossAxisAlignment, id} : namedParametersRow){
    var element = document.createElement("div");
    if(id == null || id == undefined)
        element.setAttribute("id", "Column-" + ramdomString(7));
    else
        element.setAttribute("id", `${id}-` + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    let styleJson: any = {};
    
    // element.setAttribute("style", "-webkit-flex-flow: column nowrap;-ms-flex-flow: column nowrap;flex-flow: column nowrap;")
    // element.setAttribute("style", 'display: -webkit-box;display: -moz-box;display: -webkit-flex;display: -ms-flexbox;display: flex;');
    element = flexbox(element);
    element = flexDirection(element, "column");
    
    // element.style.display = "flex";
    // element.style.flexDirection = "column";
    if(mainAxisAlignment != null && mainAxisAlignment != undefined){
        // element.style.justifyContent = mainAxisAlignment.toString();
        // styleJson.justifyContent = mainAxisAlignment.toString(); 
        element = justifyContentPrefix(element, mainAxisAlignment.toString());
    }
    if(crossAxisAlignment != null && crossAxisAlignment != undefined){
        // element.style.alignItems = crossAxisAlignment.toString();
        // styleJson.alignItems = crossAxisAlignment.toString(); 
        element = alignItemsPrefix(element, crossAxisAlignment.toString());
    }

    return {"element" : element, "style" : styleJson, "type" : "Column", "child" : children};
}

interface namedParametersFlexible {
    child : any;
    flex? : number;
}

function Flexible({child, flex} : namedParametersFlexible){
    var element = document.createElement("div");
    element.setAttribute("id", "Flexible-" + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    let styleJson : any = {};
    if(flex != null && flex != undefined){
        element = flexGrowPrefix(element, flex);
    }

    return {"element" : element, "style" : styleJson, "type" : "Flexible", "child" : [child]};
}

interface namedParametersExpanded {
    child : any;
}

function Expanded({child} : namedParametersExpanded){
    var element = document.createElement("div");
    element.setAttribute("id", "Expanded-" + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    element = flexGrowPrefix(element, 29);

    return {"element" : element, "style" : {}, "type" : "Expanded", "child" : [child]};
}

interface namedParametersVisibility{
    child:any;
    visible:boolean;
    isStateLess?:boolean;
}

function Visibility({child, visible, isStateLess = false} : namedParametersVisibility){
    var element = document.createElement("div");
    element.setAttribute("id", "Visibility-" + ramdomString(7));
    if(visible == false)
        element.style.display = "none";
    

    return {"element" : element, "style" : {}, "type" : "Visibility", "child" : [child], isStateLess: isStateLess};
}

interface namedParametersInkWell{
    child:any;
    onTap:any;
}

function InkWell({child, onTap} : namedParametersInkWell){
    var element = document.createElement("div");
    element.setAttribute("id", "InkWell-" + ramdomString(7));
    
    if(onTap){
        element.addEventListener("click", onTap);
        element.classList.add("buttonFlatHover");
        element.style.cursor = "pointer";
    }

    return {"element" : element, "style" : {}, "type" : "Visibility", "child" : [child]};
}

interface namedParametersTextFormField {
    controller : TextEditingController;
    validator? : any;
    decoration?: InputDecoration;
}

function TextFormField({controller, validator, decoration} : namedParametersTextFormField){
    var input = controller.input;
    var label = document.createElement("label");
    var parrafo = document.createElement("p");
    let isLabelNull = false;

    // container.setAttribute("id", "ContainerTextFormField-" + ramdomString(7));
    label.setAttribute("id", "LabelTextFormField-" + ramdomString(7));
    input.setAttribute("id", "TextFormField-" + ramdomString(7));
    parrafo.setAttribute("id", "ParrafoTextFormField-" + ramdomString(7));

    label.style.cssText = "font-size: 13px";
    parrafo.style.cssText = "display: none; color: red; font-size: 10px; padding: 0px; margin: 3px; 0px"

    label.classList.add("labelFloating");
    input.classList.add("inputFloating");

    if(decoration != null && decoration != undefined){
        if(decoration.labelText != null && decoration.labelText != null){
            label.innerHTML = decoration.labelText;
        }else{
            isLabelNull = true;
            console.log("isLabelNull nullllllllllllllllllllllllllllllllll");
        }
    }else{
        isLabelNull = true;
    }

    function addActiveClassAndHisStyle(){
        label.classList.add('active');
        _activeColor();
    }

    
    function removeActiveClassAndHisStyle(){
        if(input.value == null || input.value == undefined || input.value == ''){
            //Le cambiaremos el color al label e input cuando no esten enfocados, esto sucedera si la la propiedad decoration.activeColor no es nul
            _normalColor();
            label.classList.remove('active');
        }
    }

    function _activeColor(){
        //Si tiene asignado un activeColor pues le ponemos ese color, de lo contrario le dejamos el color por defecto
        if(decoration != null && decoration != undefined){
            if(decoration.activeColor != null && decoration.activeColor != null){
                label.style.color = decoration.activeColor;
                // console.log("textFormField actvieColor: ", decoration.activeColor);
                // console.log("textFormField labelcssText: ", label.style.cssText);
                input.style.borderBottom = `0.19px solid ${decoration.activeColor}`;
            }
        }
    }

    function _normalColor(){
        label.style.color = "#bdb9b9";
        input.style.borderBottom = "0.19px solid #bdb9b9";
    }

    function _errorColor(){
        label.style.color = "red";
        input.style.borderBottom = "0.19px solid red";
    }



    input.addEventListener('focus', () => {
        if(input.classList.contains("error"))
            _errorColor();
        else
            addActiveClassAndHisStyle();
    });
    input.addEventListener('blur', () => {
        if(input.classList.contains("error"))
            _errorColor();
        else
            removeActiveClassAndHisStyle()
    });
    
    // container.appendChild(label);
    // container.appendChild(input);
    // container.appendChild(parrafo);
    var container;
    if(isLabelNull){
        input.style.padding = "0px";
        container = Column({
            children: [
                {"element" : input, "child": []},
                {"element" : parrafo, "child": []},
            ]
        });
    }else{
        container = Column({
            children: [
                {"element" : label, "child": []},
                {"element" : input, "child": []},
                {"element" : parrafo, "child": []},
            ]
        });
    }
     

    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // element.style.flexGrow = `20`;

    //La variable validator es una function que se invoca desde TextFormField y esta retorna null si es valido
    // y retorna un String en caso contrario con un mensaje  indicando el error de validacion
    if(validator != null && validator != undefined){
        //Creamos el evento personalizado (custom event)
        // var event = new Event('build');
        // Escucha para el evento. Este evento solo se lanzara desde un FormGlobalKey
        input.addEventListener('validate', validate, false);
        // Disparar event.
        // elem.dispatchEvent(event);  
    }

    function validate(e : any){
        
        var resultado = validator(input.value);
        // console.log("input validate event: ", resultado, " value:", input.value);
        //Si el resultado es diferente de nulo entonces eso quiere decir que el input no es validor
        // asi que se lanzara una exception para el la function validate() del FormGlobalKey retorne false
        // indicando que el formulario no esta valido
        
        
        if(resultado != null){
            parrafo.style.display = "block";
            parrafo.innerHTML = resultado;
            parrafo.classList.add("error");
            _errorColor();
            //Enviamos el callback al formKey para inficarle que hay campos no validor
            e.detail("No valido");
        }else{
            parrafo.style.display = "none";
            parrafo.nodeValue = resultado;
            if(label.classList.contains("active")){
                if(decoration != null && decoration != undefined){
                    if(decoration.activeColor != null && decoration.activeColor != null){
                        label.style.color = decoration.activeColor;
                        input.style.borderBottom = `0.19px solid ${decoration.activeColor}`;
                    }
                }else{
                    label.style.color = "#bdb9b9";
                    input.style.borderBottom = "0.19px solid #bdb9b9";
                }
            }else{
                label.style.color = "#bdb9b9";
                input.style.borderBottom = "0.19px solid #bdb9b9";
            }
            
        }
    }

    return container;
    return {"element" : container, "style" : {}, "type" : "TextFormField", "child" : []};
}

//TextField
interface namedParametersTextField{
    onChanged?:any;
    decoration?:any;
    value?:any;
}
function TextField({onChanged, decoration, value} : namedParametersTextField){
    var input = document.createElement("input");
    var label = document.createElement("label");
    let isLabelNull = false;

    // container.setAttribute("id", "ContainerTextFormField-" + ramdomString(7));
    label.setAttribute("id", "LabelTextFormField-" + ramdomString(7));
    input.setAttribute("id", "TextField-" + ramdomString(7));

    label.style.cssText = "font-size: 13px";

    label.classList.add("labelFloating");
    input.classList.add("inputFloating");
    if(value)
        input.value = value;
    else
        input.value = "";

    if(decoration != null && decoration != undefined){
        if(decoration.labelText != null && decoration.labelText != null){
            label.innerHTML = decoration.labelText;
        }else{
            isLabelNull = true;
            console.log("isLabelNull nullllllllllllllllllllllllllllllllll");
        }
    }else{
        isLabelNull = true;
    }

    function addActiveClassAndHisStyle(){
        label.classList.add('active');
        _activeColor();
    }

    
    function removeActiveClassAndHisStyle(){
        if(input.value == null || input.value == undefined || input.value == ''){
            //Le cambiaremos el color al label e input cuando no esten enfocados, esto sucedera si la la propiedad decoration.activeColor no es nul
            _normalColor();
            label.classList.remove('active');
        }
    }

    function _activeColor(){
        //Si tiene asignado un activeColor pues le ponemos ese color, de lo contrario le dejamos el color por defecto
        if(decoration != null && decoration != undefined){
            if(decoration.activeColor != null && decoration.activeColor != null){
                label.style.color = decoration.activeColor;
                // console.log("textFormField actvieColor: ", decoration.activeColor);
                // console.log("textFormField labelcssText: ", label.style.cssText);
                input.style.borderBottom = `0.19px solid ${decoration.activeColor}`;
            }
        }
    }

    function _normalColor(){
        label.style.color = "#bdb9b9";
        input.style.borderBottom = "0.19px solid #bdb9b9";
    }




    //Events
    input.addEventListener('focus', () => {
        addActiveClassAndHisStyle();
    });
    input.addEventListener('blur', () => {
        removeActiveClassAndHisStyle()
    });
    if(onChanged)
        input.addEventListener("input", (e)=>{
            onChanged(input.value);
        });
    // container.appendChild(label);
    // container.appendChild(input);
    // container.appendChild(parrafo);
    var container;
    if(isLabelNull){
        input.style.padding = "0px";
        container = Column({
            children: [
                {"element" : input, "child": []},
            ]
        });
    }else{
        container = Column({
            children: [
                {"element" : label, "child": []},
                {"element" : input, "child": []},
            ]
        });
    }
     


    return container;
    return {"element" : container, "style" : {}, "type" : "TextFormField", "child" : []};
}

function Icon(icon: Icons) : Widget{
    var element = document.createElement("span");
    element.setAttribute("id", "Icon-" + ramdomString(7));
    element.classList.add("material-icons");
    element.appendChild(document.createTextNode(icon.toString()));
    return {element : element, child : [], type: "Icon", style : {}};
}

//Combo box widget

interface namedParametersDropDownMenuItem{
    child:any;
    value:string;
}
class DropDownMenuItem{
    child:any;
    value:string;
    constructor({child, value} : namedParametersDropDownMenuItem){
        this.child = child;
        this.value = value;
    }
    
    toJson(){
        this.child.value = this.value;
        return this.child;
    }
}

interface namedParametersDropdownButton{
    items:DropDownMenuItem[];
    onChanged:any;
    value:any;
}

function DropdownButton({items, onChanged, value} : namedParametersDropdownButton){
    var texto = Texto(value, new TextStyle({}));
    var icon = Icon(Icons.arrow_drop_down);
    var rowTextAndICon = Row({
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
        texto,
        icon
    ]});

    
    var columnDropdown = Column({
        children: items.map((dropDownMenuItem) => {
            // var event = new CustomEvent("myOnChanged", {detail: dropDownMenuItem.value});
            dropDownMenuItem.child.element.classList.add("dropdownItem");
            dropDownMenuItem.child.element.addEventListener("click", (event:any) => {event.stopPropagation(); beforeCallOnChangedCallBack(dropDownMenuItem.value)});
            //Como dropdownMenuItem no es un elemento, sino solamente 
            //un widget que me permitira obtener el valor a retornar en la funcion onChanged
            //entonces debo retornar el elemento a crear
            //en este caso el elemento a crear es el hijo del dropDownMenuItem, asi que lo retornamos
            return Container({
                child: dropDownMenuItem.child
            });
        }),
        id: "Dropdown"
    });
    columnDropdown.element.style.cssText += `visibility: hidden; background: white; z-index: 99; position: absolute; top: 20px; box-shadow: 0px 1.5px 5px 4px #f1f1f1; max-height: 340px; overflow-y: scroll; scrollbar-width: 10px; min-width: 200px;`;

    

    function beforeCallOnChangedCallBack(value:string){
        columnDropdown.element.style.visibility = "hidden";
        onChanged(value);
    }
    

    
    

    

   



    
    // container.appendChild(label);
    // container.appendChild(input);
    // container.appendChild(parrafo);
    var containerDropDown = Column({
        children: [
            rowTextAndICon,
            columnDropdown,
        ],
        id: "ContainerDropDown"
    });
    containerDropDown.element.style.cssText = "border-bottom: 1px solid #c1c1c1;"
    // columnDropdown.element.style.minWidth = `${containerDropDown.element.clientWidth}`;
    // console.log("minWidth columnDropdown: ", containerDropDown.element.client);
    

    

    //Cuado se haga click en otra parte que no sea el dropdown pues este se cerrara
    window.addEventListener("click", function(){
        if(columnDropdown.element.style.visibility == "visible"){
            // console.log("dentro windows: ", culo.classList.contains("open"), " ", culo.style.visibility);
            columnDropdown.element.style.visibility = "hidden";
        }
    })

      containerDropDown.element.addEventListener("click", function(event){
        event.stopPropagation();
        // console.log("Dentrtooooo");
        if(columnDropdown.element.style.visibility == "visible")
            return;
        columnDropdown.element.style.visibility ="visible";
        columnDropdown.element.style.width = containerDropDown.element.style.width;
        //animamos el dropdown
        columnDropdown.element.animate([
            // keyframes
            // { transform: 'translateY(0px)' }, 
            // { transform: 'translateY(50px)' }
            { transform: 'scale(0)' }, 
            { transform: 'scale(1)' }
            ], 
            { 
                // timing options
                duration: 100,
                // iterations: Infinity
            }
        );
        
      })

      

    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // element.style.flexGrow = `20`;

    //La variable validator es una function que se invoca desde TextFormField y esta retorna null si es valido
    // y retorna un String en caso contrario con un mensaje  indicando el error de validacion
    

    return containerDropDown;
    // return {"element" : container, "style" : {}, "type" : "TextFormField", "child" : []};
}

interface namedParametersCheckBox{
    value:boolean,
    onChanged:any;
    labelText?:string;
}

function CheckBox({value, onChanged, labelText} : namedParametersCheckBox){
    //To make checkbox widget equals to my template checkbox, i have to take the html code below as reference
    // <div class="form-group">
    //     <div class="form-check">
    //         <label class="form-check-label">
    //         <input class="form-check-input" type="checkbox" value="" checked> Activa
    //         <span class="form-check-sign">
    //             <span class="check"></span>
    //         </span>
    //         </label>
    //     </div>
    // </div>



    var divFormGroup = document.createElement("div");
    var divFormCheck = document.createElement("div");
    var label = document.createElement("label");
    var input = document.createElement("input");
    
    
    var spanFormCheckSign = document.createElement("span");
    var span = document.createElement("span");
    
    divFormGroup.classList.add("form-group");
    divFormCheck.classList.add("form-check");
    label.classList.add("form-check-label");
    if(labelText)
        label.innerHTML = labelText;
    input.setAttribute("type", "checkbox");
    input.checked = value;
    input.onchange = function(e){
        onChanged(input.checked);
    }
    // addEventListener("onchange", function(e){
        
    // })
    input.classList.add("form-check-input");
    spanFormCheckSign.classList.add("form-check-sign");
    span.classList.add("check");

    var spanWidget = {"element" : span, "child" : []};
    var spanFormCheckSignWidget = {"element" : spanFormCheckSign, "child" : [spanWidget]};
    var inputWidget = {"element" : input, "child" : []};
    var labelWidget = {"element" : label, "child" : [inputWidget, spanFormCheckSignWidget]};
    var divFormCheckWidget = {"element" : divFormCheck, "child" : [labelWidget]};
    var divFormGroupWidget = {"element" : divFormGroup, "child" : [divFormCheckWidget]};

    return divFormGroupWidget;
}

interface namedParametersDropdownButtonMultiple{
    items:DropDownMenuItem[];
    onChanged:any;
    selectedValues:string[];
}


function DropdownButtonMultiple({items, onChanged, selectedValues = []} : namedParametersDropdownButtonMultiple){
    let values:string = (selectedValues.length > 0) ? selectedValues.toString() : "Seleccionar";
    let valuesToReturn:string[] = (selectedValues != null && selectedValues != undefined) ? selectedValues : [];
    
    //fila para mostrar texto e icono principal
    var texto = Texto(values, new TextStyle({}), true);
    var icon = Icon(Icons.arrow_drop_down);
    var rowTextAndICon = Row({
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
        texto,
        icon
    ]});

   

    //Column dropdown
    var columnDropdown = Column({
        children: items.map((dropDownMenuItem) => {
            // var event = new CustomEvent("myOnChanged", {detail: dropDownMenuItem.value});
            // dropDownMenuItem.child.element.classList.add("dropdownItem");
            // dropDownMenuItem.child.element.addEventListener("click", (event:any) => {event.stopPropagation(); addOrValue(dropDownMenuItem.value)});
            //Como dropdownMenuItem no es un elemento, sino solamente 
            //un widget que me permitira obtener el valor a retornar en la funcion onChanged
            //entonces debo retornar el elemento a crear
            //en este caso el elemento a crear es el hijo del dropDownMenuItem, asi que lo retornamos
            
            //Creamos el visibility en una variable, para asi enviarla al callback 
            //addOrRemoveValue asi podremos mostrar u ocultar el icono check
            let visibility = Visibility({
                child: Icon(Icons.done),
                visible: false,
                isStateLess: true
            });
            dropDownMenuItem.child = Row({
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                    dropDownMenuItem.child,
                    visibility
                ]
            });

            dropDownMenuItem.child.element.classList.add("dropdownItem");
            dropDownMenuItem.child.element.addEventListener("click", (event:any) => {event.stopPropagation(); addOrRemoveValue(dropDownMenuItem.value, visibility)});
            return dropDownMenuItem.child;
        }),
        id: "Dropdown"
    });
    columnDropdown.element.style.cssText += `visibility: hidden; background: white; z-index: 99; position: absolute; top: 20px; box-shadow: 0px 1.5px 5px 4px #f1f1f1; max-height: 340px; overflow-y: scroll; scrollbar-width: 10px; min-width: 200px`;

    //Row actions, listo o cancelar
    var btnListo = FlatButton({child: Texto("Listo", new TextStyle({})), onPressed: (e:MouseEvent) => {e.stopPropagation(); beforeCallOnChangedCallBack()}});
    var btnCancelar = FlatButton({child: Texto("Cancelar", new TextStyle({})), onPressed: (e:MouseEvent) => {e.stopPropagation(); beforeCallOnChangedCallBack()}});
    var rowActions = Row({
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
            btnCancelar,
            btnListo
        ]
    });
    columnDropdown.child.push(rowActions);

    function addOrRemoveValue(value:string, visibility:any){
        if(valuesToReturn.length == 0){
            visibility.element.style.display = "block";
            addValue(value);
        }
        else if(valuesToReturn.indexOf(value) == -1){
            visibility.element.style.display = "block";
            addValue(value);
        }
        else{
            visibility.element.style.display = "none";
            removeValue(value);
        }
    }

    function addValue(value:string){
        valuesToReturn.push(value);
    }

    function removeValue(value:string){
        let index:number = valuesToReturn.indexOf(value);
        if(index != -1)
            valuesToReturn.splice(index, 1);
    }

    function beforeCallOnChangedCallBack(){
        columnDropdown.element.style.visibility = "hidden";
        closeDropdown();
        onChanged(valuesToReturn);
        // console.log("beforeCallOnChangedCallBack: ", valuesToReturn);
        if(valuesToReturn.length == 0)
            texto.element.innerHTML = "Seleccionar...";
        else
            texto.element.innerHTML = valuesToReturn;
    }
    
    function closeDropdown(){
        columnDropdown.element.style.visibility = "hidden";
    }
    
    

    

   



    
    // container.appendChild(label);
    // container.appendChild(input);
    // container.appendChild(parrafo);
    var containerDropDown = Column({
        children: [
            rowTextAndICon,
            columnDropdown,
        ],
        id: "ContainerDropdownButtonMultiple"
    });
    containerDropDown.element.style.cssText = "border-bottom: 1px solid #c1c1c1;"

    

    //Cuado se haga click en otra parte que no sea el dropdown pues este se cerrara
    window.addEventListener("click", function(){
        if(columnDropdown.element.style.visibility == "visible"){
            // console.log("dentro windows: ", culo.classList.contains("open"), " ", culo.style.visibility);
            columnDropdown.element.style.visibility = "hidden";
            beforeCallOnChangedCallBack();
        }
    })

      containerDropDown.element.addEventListener("click", function(event){
        event.stopPropagation();
        // console.log("Dentrtooooo");

        
        if(columnDropdown.element.style.visibility == "visible")
            return;
        columnDropdown.element.style.visibility ="visible";
        //animamos el dropdown
        columnDropdown.element.animate([
            // keyframes
            // { transform: 'translateY(0px)' }, 
            // { transform: 'translateY(50px)' }
            { transform: 'scale(0)' }, 
            { transform: 'scale(1)' }
            ], 
            { 
                // timing options
                duration: 100,
                // iterations: Infinity
            }
        );
        
      })

      

    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // element.style.flexGrow = `20`;

    //La variable validator es una function que se invoca desde TextFormField y esta retorna null si es valido
    // y retorna un String en caso contrario con un mensaje  indicando el error de validacion
    

    return containerDropDown;
    // return {"element" : container, "style" : {}, "type" : "TextFormField", "child" : []};
}



interface namedParametersBuilder{
    id: string;
    builder: any;
    initState?:any;
}

function Builder({id, builder, initState} : namedParametersBuilder){
    var element = document.getElementById(id);
    if(element == null){
        element = document.createElement("div");
        element.setAttribute("id", "container");
    }

    element.innerHTML = "";
    interface Prueba {
        (mensaje : string) : void
    }

    if(initState){
        window.onload = function () {
            // rebuildSizeFromLayoutBuilder();
            initState();
        };
    }else{
        window.onload = function () {
            // rebuildSizeFromLayoutBuilder();
        };
    }

    var setState =( function(){
        // callback;
        // console.log("setState: ", callback);
        element?.classList.add("setState");
        // while (element.firstChild) {
            // element.removeChild(element.firstChild);
            // var c = document.getElementsByClassName("cambios");
            // var event = new Event("cambios");
            // for(var i = 0; i < c.length; i++){
            //     console.log("dentro setState hijos: ", c[i]);
            //     c[i].dispatchEvent(event);
            // }
            
        // }
        // console.log("builder setstate id: ", element?.id);
        
        var elements = builder(element?.id, setState);
        
        var widgetsYaCreados = Array.from(element?.childNodes);
        builderArrayRecursivo(elements, widgetsYaCreados, true, true);
        oncreatedOrUpdatedWidgetState();
        
    }).bind(element);
    var elements = builder(id, setState);
    builderArrayRecursivo(elements, null, true);
    oncreatedOrUpdatedWidgetState();
}

interface namedParametersStreamBuilder{
    stream:StreamController;
    builder:any;
}

function StreamBuilder({stream, builder} : namedParametersStreamBuilder){
    var element = stream.div;
    let snapshot:any;    
    

    element.addEventListener('rebuild', rebuild, false);
    // element.addEventListener('dispose', dispose, false);
    // var myInterval = setInterval(rebuild, 1000);

    // function dispose(){
    //     clearInterval(myInterval);
    // }

    function rebuild(){
        var elements = builder(element.id, stream.data);
        console.log("streambuilder rebuild: ", elements);
        elements = Init({
            id: element.id,
            child: elements
        });

        
        
        // console.log("streambuilder rebuild new: ", elements);
        
        var widgetsYaCreados = Array.from(element?.childNodes);
        // console.log("streambuilder rebuild ya creados: ", widgetsYaCreados);
        builderArrayRecursivo(elements, widgetsYaCreados, true, true);
    }
   
    var child = builder(element.id, stream.data);
    // console.log("Resultadooooooooooooos streambuilder child: ", child);
    return {element: element, type: "StreamBuilder", child: [child]}
}

interface namedParametersLayoutBuilder{
    id?:string;
    builder:any;
}

window.addEventListener('resize', rebuildSizeFromLayoutBuilder);

//Esta funcion se llama desde dos partes, desde el evento window.resize y desde el builder.initState
//para que asi el layout builder tome el tamano del padre
function rebuildSizeFromLayoutBuilder(){
    
    var elements = document.getElementsByClassName("LayoutBuilder");
    for(var i=0; i < elements.length; i++){
        //Mandamos el id del elemento y el size con su ancho y alto
        // var size = {height: window.innerHeight, width: window.innerWidth};
        var parentSize = getParentSize(elements[i]);
        // console.log("Size from rebuildSizeFromLayoutBuilder parentSize: ", parentSize);
        // console.log("Size from rebuildSizeFromLayoutBuilder LayoutBuilder: ", elements[i].id);
        
        let event = new CustomEvent("rebuildSize", {detail: {id: elements[i].id, size:parentSize}});
        elements[i].dispatchEvent(event)
    }
}

function getParentSize(element:Element){
    var parent = element.parentElement;
    let size:any = {width: 0, height: 0};
    let vecesARecorrer = 6;
    for (var i=0; i < vecesARecorrer; i++) {
        // console.log(`getParentSize: `, parent.id, " ", parent.offsetWidth, " ", parent.style.width);
        
        if(parent == null || parent == undefined)
            break;
        
        if(parent.clientWidth > 0){
            console.log("Dentro clienteWidth");
            
            size.width = parent.clientWidth;
        }
        else if(parent.style.width)
            {
                console.log("Dentro width");
                size.width = parent.style.width.replace("px", "");}
        if(parent.clientHeight > 0)
            size.height = parent.clientHeight;
        else if(parent.style.height)
            size.height = parent.style.height.replace("px", "");

        // if(parent.style.width)
        //     size.width = parent.style.width.replace("px", "");
        // if(parent.style.height)
        //     size.height = parent.style.height.replace("px", "");
        
        //Si el width es > 0 entonces salimos del ciclo y retornamos la variable size
        //de lo contrario vamos a tomar el padre del otro elemento padre y asi sucesivamente hasta
        //que encontrar el size o hasta que el sigueinte padre sea nulo
        if(size.width > 0)
            break;
        else
            parent = parent.parentElement;
    }
    
    return size;
}

function LayoutBuilder({builder} : namedParametersLayoutBuilder){
    var element = document.createElement("div");
    element.setAttribute("id", `LayoutBuilder-${ramdomString(7)}`);
    element.classList.add("LayoutBuilder");
    

    element.addEventListener('rebuildSize', rebuildSize, false);
    // element.addEventListener('dispose', dispose, false);
    // var myInterval = setInterval(rebuild, 1000);

    // function dispose(){
    //     clearInterval(myInterval);
    // }

    function rebuildSize(e){
        //El parametro e va a container el id del layout builder y el size 
        //de la venta, osea, window.innerWidth and window.innerHeight, pero ese size no me funciona
        //por el size que necesito es el size del parent, asi que lo busco y lo tomo
        var elementLayoutBuilder = document.getElementById(e.detail.id);
        //buscamos el padre
        // var parent = elementLayoutBuilder.parentElement;
        //obtenemos el size del padre
        // let sizeParent = {width: parent.clientWidth, height: parent.clientHeight};
        //le mandamos el size del padre al builder
        var elements = builder(e.detail.size);
        elements = Init({
            id: elementLayoutBuilder.id,
            child: elements
        });

        
        
        // console.log("streambuilder rebuild new: ", elements);
        
        var widgetsYaCreados = Array.from(elementLayoutBuilder?.childNodes);
        // console.log("streambuilder rebuild ya creados: ", widgetsYaCreados);
        builderArrayRecursivo(elements, widgetsYaCreados, true, true);
    }
    var size = {width: window.innerWidth, height: window.innerHeight};
    var child = builder(size);
    // console.log("Resultadooooooooooooos LayoutBuilder size: ", size);
    // console.log("Resultadooooooooooooos LayoutBuilder sizeParent: ", element.parentElement);
    return {element: element, type: "LayoutBuilder", child: [child]}
}


interface namedParametersForm {
    key : FormGlobalKey;
    child : any
}

function Form({key, child} : namedParametersForm){
    var element = key.form;
    element.setAttribute("id", "Form-" + ramdomString(5));

    return {"element" : element, "child" : [child]};
}

interface namedParametersRaisedButton {
    onPressed : any;
    child : any;
    color? : any;
}

function RaisedButton({child, onPressed, color = "#1a73e8"} : namedParametersRaisedButton){
    // var element = document.createElement("div");
    // element.setAttribute("id", ramdomString(5));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // var css = 'table td:hover{ background-color: rgba(0,0,0,0.8); filter:brightness(0.9); } ';
    
    var container = Container({id: "RaisedButton", child: child, style: new TextStyle({background: color, cursor: "pointer",padding: EdgetInsets.only({left: 20, right: 20, bottom: 7, top: 7}), borderRadius: BorderRadius.all(4)})});
    if(onPressed)
        container.element.addEventListener("click", onPressed);

    container.element.classList.add("buttonHover")

    // container.element.style.background = "yellow";
    // container.element.style.padding = "5px 10px 5px 10px";
    // container.element.style.height = "10px";
    return container;
}

interface namedParametersFlatButton {
    onPressed : any;
    child : any;
    color? : any;
}

function FlatButton({child, onPressed, color = "#1a73e8"} : namedParametersFlatButton){
    // var element = document.createElement("div");
    // element.setAttribute("id", ramdomString(5));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // var css = 'table td:hover{ background-color: rgba(0,0,0,0.8); filter:brightness(0.9); } ';
    
    var container = Container({id: "FlatButton", child: child, style: new TextStyle({color: color, cursor: "pointer",padding: EdgetInsets.only({left: 14, right: 14, bottom: 7, top: 7}), borderRadius: BorderRadius.all(4)})});
    if(onPressed)
        container.element.addEventListener("click", onPressed);

    container.element.classList.add("buttonFlatHover");

    // container.element.style.background = "yellow";
    // container.element.style.padding = "5px 10px 5px 10px";
    // container.element.style.height = "10px";
    return container;
}

interface namedParametersSizedBox {
    child? : any;
    width? : number;
    height? : number;
}

function SizedBox({child, width, height} : namedParametersSizedBox){
    var element = document.createElement("div");
    element.setAttribute("id", 'SizedBox-' + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    
    if(width)
        element.style.width = `${width}${fontSizeUnit}`;
    if(height)
        element.style.height = `${height}${fontSizeUnit}`;

    if(child != null && child != undefined)
        return {"element" : element, "child" : [child]};
    else
        return {"element" : element, "child" : []};
}

interface namedParametersAlign{
    child:any;
    alignment:Alignment;
}

function Align({child, alignment} : namedParametersAlign){
    var element = document.createElement("div");
    element.setAttribute("id", 'Align-' + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    element.style.position = "relative";
    child.element.style.cssText += `position: absolute; ${alignment.toString()}`;
    

    return {"element" : element, "child" : [child]};
}

interface namedParametersPadding{
    child:any;
    padding:EdgetInsets;
}

function Padding({child, padding} : namedParametersPadding){
    var element = document.createElement("div");
    element.setAttribute("id", 'Padding-' + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    
    element.style.padding = padding.toString();
    
    return {"element" : element, "child" : [child]};
}

interface namedParametersCircularProgressIndicator{
    color?:string;
}

function CircularProgressIndicator({color = '#3498db'} : namedParametersCircularProgressIndicator){
    var element = document.createElement("div");
    element.setAttribute("id", 'SizedBox-' + ramdomString(7));
    // var defaultStyle = {"display" : "flex", "flex-direction" : "row"};
    // element.style.cssText = `border: 2px solid #f3f3f3; border-top: 2px solid ${color};  border-radius: 50%; width: 18px; height: 18px;`;
    element.style.cssText = `border-top: 2px solid ${color}; border-right: 2px solid ${color};  border-radius: 50%; width: 18px; height: 18px;`;
    element.animate([
        // keyframes
        // { transform: 'translateY(0px)' }, 
        // { transform: 'translateY(50px)' }
        { transform: 'rotate(0deg)' }, 
        { transform: 'rotate(360deg)' }
        ], 
        { 
            // timing options
            duration: 1400,
            iterations: Infinity
        }
    );
    if(color != null && color != undefined)
        element.style.color = color;
    // if(width)
    //     element.style.width = `${width}${fontSizeUnit}`;
    // if(height)
    //     element.style.height = `${height}${fontSizeUnit}`;

    // if(child != null && child != undefined)
    //     return {"element" : element, "child" : [child]};
    // else
        return {"element" : element, "child" : []};
}

class Utils{
    static headers:any = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
}

class StreamController{
    div:HTMLDivElement;
    data:any;
    constructor(){
        this.div = document.createElement("div");
        this.div.setAttribute("id", "StreamController-" + ramdomString(7));
    }

    add(data:any){
        this.data = data;
        let event = new CustomEvent("rebuild", {detail: data})
        this.div.dispatchEvent(event);
    }
}

interface namedParametersGet{
    url:string;
    headers:any;
}

interface namedParametersPost{
    url:string;
    headers:any;
    data:any;
}

class http{
    static async get({url, headers}:namedParametersGet){
        try {
            var myRequest = new Request(url, headers);
            let response:any = await fetch(myRequest);
            let data:any = await response.json();
            return data;
        } catch (error) {
            console.log("Error http get: ", error);
            throw new Error(`error: ${error}`);
        }
        
    }

    static async post({url, headers, data}:namedParametersPost){
        try {
            var myRequest = new Request(url, headers);
            let response:any = await fetch(url, {method: "POST", body: JSON.stringify(data), headers: headers});
            let responseData:any = await response.json();
            return responseData;
        } catch (error) {
            console.log("Error http get: ", error);
            throw new Error(`error: ${error}`);
        }
        
    }
}

function builderRecursivo(widget : any, isInit = false, widgetsYaCreados : any = null){
    if((widget.child == null || widget.child == undefined ) && Array.isArray(widget) == false)
        return 0;
    var hijosDelElementoInit;
    if(isInit){
        hijosDelElementoInit = document.getElementById(widget.element.id)?.childNodes;
    }
    else if(isInit == false && widgetsYaCreados != null){
        hijosDelElementoInit = widgetsYaCreados.childNodes;
    }

    if(hijosDelElementoInit != null)
        if(hijosDelElementoInit.length > 0)
            widgetsYaCreados = hijosDelElementoInit;

    

    if(widgetsYaCreados == null || widgetsYaCreados == undefined){
        // console.log("builderRecursivo Dentro widgetsYaCreados null: ", widget);
        
        if(Array.isArray(widget.child)){
            // widget.element.appendChild()
            builderArrayRecursivo(widget);
        }else{
            widget.element.appendChild(widget.child.element);
            builderRecursivo(widget.child);
        }
    }else{
        
        if(Array.isArray(widget.child)){
            // widget.element.appendChild()
            
            // console.log("Dentro array: ", widget.child);
            const array = Array.from(widgetsYaCreados);
            // console.log("Dentro array: ", array);
            builderArrayRecursivo(widget, array);
        }else{
            // widgetsYaCreados.shift();
            // console.log("builderRecursivo widgetsYaCreados: ", widgetsYaCreados);
            // console.log("builderRecursivo widgets a crear: ", widget);
            // console.log("Dentrooooooooooooooooooooooooooooooooooo: ", widgetsYaCreados);
            // console.log("Dentroooooooooooooooooooooooooo a crearrr: ", widget.child.element);
            
            if(widgetsYaCreados.length == 1){
                var widgetCreado = widgetsYaCreados[0];
                if(widget.child.type == widgetCreado.id.split("-")[0] && widget.child.element.nodeName == widgetCreado.nodeName && widget.child.element.nodeType == widgetCreado.nodeType){
                    updateStyleOfExistenteWidget(widget, widgetCreado);
                    updateTextOfExistenteWidget(widget, widgetCreado);
                    widget.child.element = widgetCreado;
                    builderRecursivo(widget.child, false, widgetCreado.childNodes);
                    
                }else{
                    //Tomamos el nodo padre
                    var parent = widgetCreado.parentNode;
                    //Creamos el nuevo nodo
                    widget.element.appendChild(widget.child.element);
                    //Eliminamos el viejo nodo
                    parent.removeChild(widgetCreado);
                    builderRecursivo(widget.child, false, widgetCreado.childNodes);
                }
            }else{
                // console.log("Son diferente");
                // return;
                widget.element.appendChild(widget.child.element);
                builderRecursivo(widget.child);
            }
            
        }
    }
    
    
}



function builderArrayRecursivo(widget : any, widgetsYaCreados? : any, isInit: boolean = false, onlyWidgetsYaCreados: boolean = false, widgetInit = null){
    // console.log("recursiveArray widget: ", widget);
    if(isInit){
        widgetInit = widget;
    }
    
   if((widgetsYaCreados == null || widgetsYaCreados == undefined) && onlyWidgetsYaCreados == false){
        //Veriricamos de que el hijo sea un array para recorrerlo recursivamente
        var idWidget = (widgetInit != null) ? widgetInit.id : null;
        // console.log("Widget termnoooooo create: ", widget.id, " ", idWidget);
        if(!Array.isArray(widget.child))
            {
                // console.log("Widget termnoooooo create: ", widget);
                return;
            }

            // console.log("Dentro widgetsYacreados null: ", onlyWidgetsYaCreados);
            

        //Si el tamano del arreglo hijo es cero entonces ya no hay que recorrer nada asi que retornamos para salir de la funcion
        if(widget.child.length <= 0){
            //Cuando esta condicion se cumple eso quiere decir que ya se han creados todos los elementos
            //basicamente, es como un evento que se lanza cuando todos los elementos o cambios ya se han agregados al dom
            // oncreatedOrUpdatedWidgetState();
            // let isWidgetEqualToWidgetInit:boolean = false;
            // if(widgetInit != null)
            //     isWidgetEqualToWidgetInit = widget.id == widgetInit.id;
            // //Si la variable isWidgetEqualToWidgetInit == true entonces eso quiere decir
            // //que la funcion recursiva va a terminar su ejecucion
            // if(isWidgetEqualToWidgetInit)
            //     oncreatedOrUpdatedWidgetState();
            return;
        }

        //Eliminamos y optenemos el primer elemento(widget) del arreglo hijo, asi el tamano del arreglo se va reduciendo
        var hijo = widget.child.shift();

        //el atributo element es el elemento html o nodo que pertenece al widget hijo
        if(hijo.element == null){
            //Cuando esta condicion se cumple eso quiere decir que ya se han creados todos los elementos
            //basicamente, es como un evento que se lanza cuando todos los elementos o cambios ya se han agregados al dom
            // oncreatedOrUpdatedWidgetState();
            return;
        }

        //Al widget el anadimos el widget hijo que obtuvimos y eliminamos del arreglo
        // console.log("builderArrayRecursivo: ", hijo);
        
        widget.element.appendChild(hijo.element);

        //Si el widget hijo tiene mas hijos entonces lo recorreremos recursivamente, para eso llamamos a la funcion builderRecursvio
        if(hijo.child != null)
            builderRecursivo(hijo);

        //Llamamos a esta misma funcion para seguir recorriendo de manera recursiva
        builderArrayRecursivo(widget, null, false, false, widgetInit);
   }else{
    
    // console.log("widgetNuevo: ", widget);
         //Veriricamos de que el hijo sea un array para recorrerlo recursivamente
        if(!Array.isArray(widget.child)){
            // oncreatedOrUpdatedWidgetState();
            return;
        }
            

        //Si el tamano del arreglo hijo es cero entonces ya no hay que recorrer nada asi que retornamos para salir de la funcion
        if(widget.child.length <= 0){
            // oncreatedOrUpdatedWidgetState();
            
            let isWidgetEqualToWidgetInit:boolean = false;
            if(widgetInit != null)
                isWidgetEqualToWidgetInit = widget.element.id == widgetInit.element.id;
            //Si la variable isWidgetEqualToWidgetInit == true entonces eso quiere decir
            //que la funcion recursiva va a terminar su ejecucion
            // console.log("isWidgetEqualToWidgetInit: ", widget.element.id, " ", widgetInit.element.id);
            
            if(isWidgetEqualToWidgetInit){
                // console.log("Widget a crear: ", widget.element.id);
                // oncreatedOrUpdatedWidgetState();
            }
            return;
        }

        
        

        //Eliminamos y optenemos el primer elemento(widget) del arreglo hijo, asi el tamano del arreglo se va reduciendo
        var hijo = widget.child.shift();
        // console.log("Dentro widgetCreado == delete: ", hijo);
        // console.log("builderArrayRecursivo: ", hijo);
        if(widgetsYaCreados != null){
            // if(widgetsYaCreados.length == null || widgetsYaCreados.length == undefined){
            //     widgetsYaCreados = Array.from(widgetsYaCreados.childNodes);
            // }
            if(Array.isArray(widgetsYaCreados) == false){
                widgetsYaCreados = Array.from(widgetsYaCreados.childNodes);
            }
            else if(widgetsYaCreados.length == 0){
                // console.log("builerArrayRecursivo: ", widgetsYaCreados.childNodes, " ", widgetsYaCreados.length);
                if(widgetsYaCreados.childNodes != undefined && widgetsYaCreados.childNodes != null)
                    widgetsYaCreados = Array.from(widgetsYaCreados.childNodes);
                // console.log("Dentro legnt ==0");
                
                if(widgetsYaCreados.length == 0)
                    widgetsYaCreados = null;
            }
            // else if(Array.isArray(widgetsYaCreados) == false)
            //     widgetsYaCreados = Array.from(widgetsYaCreados);
        }
        
        // console.log("builerArrayRecursivo: ", widgetCreado, " ", widgetsYaCreados.length);
        // console.log("builer: ", widgetsYaCreados.length, " ", Array.isArray(widgetsYaCreados));
        
        var widgetCreado =(widgetsYaCreados != null && widgetsYaCreados != undefined) ? widgetsYaCreados.shift() : null;
        
        
       
        

        //el atributo element es el elemento html o nodo que pertenece al widget hijo
        // if(hijo.element == null){
        //     return;
        // }

        
        if(hijo.element.id.split("-")[0] == "TextFormField"){
            
        }
        

        // console.log("builderArrayRecursivo antes de error: ", hijo.element.innerHTML);

        //Al widget le anadimos el widget hijo que obtuvimos y eliminamos del arreglo
        
        
        if(widgetCreado != null && widgetCreado != null){
            // console.log("builderArrayRecursivo comparando widget nuevo y viejo: ", hijo.type == widgetCreado.id.split("-")[0]);
            // console.log("builderArrayRecursivo ==: ", hijo.element.id.split("-")[0] == widgetCreado.id.split("-")[0], " type: ", hijo.element.id.split("-"), ":", widgetCreado.id.split("-"));
            // console.log("widget creado: ", widgetCreado);
            
            
            if(widgetCreado.id != undefined && widgetCreado.id != null){
                
                if(hijo.element.id.split("-")[0] == widgetCreado.id.split("-")[0]){
                    updateStyleOfExistenteWidget(hijo, widgetCreado);
                    updateTextOfExistenteWidget(hijo, widgetCreado);
                    hijo.element = widgetCreado;
                    // console.log("Dentro widgetCreado == update: ", hijo);
                    // console.log("builderArrayRecursivo widgetsYaCreados: ", widgetCreado);
                    // console.log("builderArrayRecursivo widgetsYaCreados: ", widgetCreado.length);
                    // console.log("builderArrayRecursivo widgetsNuevo: ", hijo);
                    // builderRecursivo(widget.child, false, widgetCreado.childNodes);
                }else{
                    // console.log("Dentro eliminar widget diferente widgetCreado: ", widgetCreado);
                    // console.log("Dentro eliminar widget diferente widgetNuevo: ", hijo);
                    // console.log("Dentroooooooo eliminarrrrrrrrrrrrrrrrr: ", widgetCreado.id);
                    // console.log("Dentroooooooo eliminarrrrrrrrrrrrrrrrr nuevo: ", hijo.element.id);
                    
                    
                    //Tomamos el nodo padre
                    var parent = widgetCreado.parentNode;
                    //Creamos el nuevo nodo
                    widget.element.appendChild(hijo.element);
                    //Eliminamos el viejo nodo
                    parent.removeChild(widgetCreado);
                    // builderRecursivo(widget.child, false, widgetCreado.childNodes);
                }
            }else{
                widget.element.appendChild(hijo.element);
            }
        }else{
            // console.log("Dentro widgetCreado == null: ", hijo);
            
            widget.element.appendChild(hijo.element);
        }
        // widget.element.appendChild(hijo.element);
        // console.log("builderArrayRecursivo: ", hijo);
        
        //Si el widget hijo tiene mas hijos entonces lo recorreremos recursivamente, para eso llamamos a la funcion builderRecursvio
        if(hijo.child != null)
            builderArrayRecursivo(hijo, widgetCreado, false, true, widgetInit);

            // console.log("builderArrayRecursivo despues: ", hijo);
        //Llamamos a esta misma funcion para seguir recorriendo de manera recursiva
        builderArrayRecursivo(widget, widgetsYaCreados, false, true, widgetInit);
   }

//    console.log("Termino terminoooooooooooooooooooooooooooooooooooooooooooooooooooooooooo");
}

function updateStyleOfExistenteWidget(nuevoWidget: any, widgetViejoOExistente: any){
    // console.log("updateStyleOfExistenteWidget nuevoWidget: ", nuevoWidget);
    if(nuevoWidget.element == null || nuevoWidget.element == undefined)
        return;
    if(nuevoWidget.style == null || nuevoWidget.style == undefined)
        return;

    
    
    // Object.keys(nuevoWidget.style).forEach(key => {
    //     widgetViejoOExistente.style[key] = nuevoWidget.style[key];
    // });
    if(nuevoWidget.isStateLess != true){
        if(nuevoWidget.element.id.split("-")[0] == "LayoutBuilder")
            console.log("viejo - nuevo: ", nuevoWidget.element.style.width, " ", widgetViejoOExistente.style.width);
            
        widgetViejoOExistente.style.cssText = nuevoWidget.element.style.cssText;
    }
    // console.log("updateStyleOfExistente: ", nuevoWidget.element.style.cssText);
    
}

function updateTextOfExistenteWidget(nuevoWidget: any, widgetViejoOExistente: any){
    // if(nuevoWidget.child != null && nuevoWidget.child != undefined){
    //     if(nuevoWidget.child.element != null && nuevoWidget.child.element != undefined)
    //     if(nuevoWidget.child.element.id.split("-") == "Texto")
    //         widgetViejoOExistente.innerHTML = nuevoWidget.child.element.innerHTML;
    // }
    if(nuevoWidget.element != null && nuevoWidget.element != undefined){
        if(nuevoWidget.element.id.split("-")[0] == "Texto"){
            //Si no es StateLessWidget(cambia), entonces vamos a cambiar el texto, de lo contrario no podemos cambiar el texto
            if(nuevoWidget.isStateLess == false)
                widgetViejoOExistente.innerHTML = nuevoWidget.element.innerHTML;
        }
        if(nuevoWidget.element.id.split("-")[0] == "TextField"){
            //Si no es StateLessWidget(cambia), entonces vamos a cambiar el texto, de lo contrario no podemos cambiar el texto
            // if(nuevoWidget.isStateLess == false)
                widgetViejoOExistente.value = nuevoWidget.element.value;
        }
            
    }
}


let _formKey = new FormGlobalKey();



interface namedParametersBuild{
    id: string;
}
declare var flutter : object;







let _mensaje: string = "hola";
let _mostrarColumna = false;
let color = "green";
// Builder({
//     id: "container",
//     builder: (id : any, setState : any) => {
//         return Init({
//             initDefaultStyle: true,
//             id: id,
//             child: Container({
//                 child: Row({
//                     children: [
//                         Texto("Jeancito", new TextStyle({color: color})),
//                         RaisedButton({
//                             child: Texto("Texto azul", new TextStyle({})),
//                             onPressed: () => {
//                                 color = "blue";
//                                 setState();
//                             }
//                         }),
//                         RaisedButton({
//                             child: Texto("Texto rojo", new TextStyle({})),
//                             onPressed: () => {
//                                 color = "red";
//                                 setState();
//                             }
//                         })
//                     ]
//                 })
//             })
//         })
//     }
// });






// var c = new Init({
//     id: "container", 
//     child: new Container({
//         child: new Texto({text: "jean"})
//     })
// });

// for(var cc in c){
//     console.log("print widget ccccc: ", cc);
// }
// console.log("print widget c: ", c.child);
function createJWT(data, key = apiKeyGlobal){
    var header = {
    "alg": "HS256",
    "typ": "JWT"
    };

    var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
    var encodedHeader = this.base64url(stringifiedHeader);

    // data = {
    // "id": 1337,
    // "username": "john.doe"
    // };

    var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
    var encodedData = this.base64url(stringifiedData);

    var token = encodedHeader + "." + encodedData;

    var signature = CryptoJS.HmacSHA256(token, key);
    signature = this.base64url(signature);

    var signedToken = token + "." + signature;
    return signedToken;
}

function base64url(source:any) {
    // Encode in classical base64
    let encodedSource:string = CryptoJS.enc.Base64.stringify(source);
  
    // Remove padding equal characters
    encodedSource = encodedSource.replace(/=+$/, '');
  
    // Replace characters according to base64url specifications
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');
  
    return encodedSource;
  }

var _txt1 = new TextEditingController();
var _txt2 = new TextEditingController();
var _actionColor = "blue";
let items: string[] = ["Valor1", "Valor2", "Valor3", "Culo", "Ripio", "tallo", "la semilla"];
var _index = 0; 


            // $http.get(rutaGlobal+"/api/bloqueos?token="+ jwt)
// console.log("jwt aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa: " + jwt);


let setState:any;
let _streamController = new StreamController();
let _streamControllerSorteo = new StreamController();
let _streamControllerLoteria = new StreamController();
let _streamControllerMoneda = new StreamController();
let _txtDirecto = new TextEditingController();
let _txtPale = new TextEditingController();
let _txtTripleta = new TextEditingController();
let _txtSuperpale = new TextEditingController();
let _txtPick3Straight = new TextEditingController();
let _txtPick3Box = new TextEditingController();
let _txtPick4Straight = new TextEditingController();
let _txtPick4Box = new TextEditingController();
let listaBanca:any[] = [];
let _indexBanca:number = 0;
let listaSorteo:any[] = [];
let _indexSorteo:number = 0;
let listaLoteria:any[] = [];
let _indexLoteria:number = 0;
let listaMoneda:any[] = [];
let _indexMoneda:number = 0;
let listaOpcion:string[] = ["General", "Por banca"];
let _indexOpcion:number = 0;
let _loterias:any[] = [];
let _bancas:any[] = [];
let _cargando:boolean = false;

interface namedParametersMyContainer{
    active:boolean;
    text:string;
}
function _myContainer({text, active}){
    let _background:string = (active) ? "#00bcd4" : "transparent";
    let _color:string = (active) ? "white" : "#00bcd4";
    return Container({
        style: new TextStyle({padding: EdgetInsets.only({left: 14, right: 14, top: 2, bottom: 2}), background: _background, border: Border.all({color: "#00bcd4"}), borderRadius: BorderRadius.all(3)}),
        child: Texto(text.toUpperCase(), new TextStyle({color: _color, fontSize: 11, fontWeight: FontWeight.w500}))
    });
}








