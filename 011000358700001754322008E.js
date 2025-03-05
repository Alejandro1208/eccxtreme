/*******************************************************************************
* essentials.js
********************************************************************************
* @file         essentials.js
*
* @version      SAP ReDesign.2007 $Rev: 0 $
* @copyright    Copyright (C) 2007 SAP AG
*
* @brief        Essential JavaScripts for SAP Partner Portal.
*
* @author       Andreas Christ <andreas.christ@sp-lion.com>
*
* @date         18.06.2009 / Andreas Mohr
*                 Funktion Email_us eingefügt
* @date         30.01.2008 / Andreas Mohr
*                 Matrix-Tabellen mit cellspacing=1
* @date         09.01.2008 10:48 / Andreas Mohr
*                 alles von sapidb auf sapidp umgestellt.
* @date         11.12.2007 12:37 / Andreas Christ
*                 Add expand_height().
* @date         12.11.2007 15:13 / Andreas Christ
*                 g_button umgestellt -> Anchor in Table
* @date         07.11.2007 15:41 / Andreas Christ
* @date         07.11.2007 14:06 / Andreas Christ
* @date         30.10.2007 22:18 / Andreas Christ
* @date         17.10.2007 16:52 / Andreas Christ
*                 Pilot.
*
*******************************************************************************/

//==============================================================================
// CLASSES
//==============================================================================


//==============================================================================
// FUNCTIONS
//==============================================================================

//----------------------------------------------------------------------------
function matrix_table()
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 17.10.2007 16:54 / Andreas Christ
// @brief   : Formatiert eine Tabelle alternierend
//            Die Tabelle muss die Klasse matrix besitzen und kann normal
//            formatiert werden. Das script erledigt den Rest.
//            Die Funktion muss mit
//                <body onload="matrix_table();">
//            aktiviert werden.
// @param   : -none-
// @return  : -none-
//----------------------------------------------------------------------------
  {
    // Klassennamen fuer gerade und ungerade Zeilen angeben
    var odd_class  = 'content-dark';
    var even_class = 'content-light';

    // Alle Tabellen ermitteln und dann nur solche mit der Klasse matrix bearbeiten
    var table_list = document.getElementsByTagName('table');
      for(var idx=0; idx<table_list.length; idx++)
        {
          var tbl_obj = table_list[idx];
            if( tbl_obj.className.match(/matrix/))
              {
								// Alle matrix-Tabellen cellspacing 1 mitgeben
								var table_spacing = document.createAttribute("cellspacing");
								table_spacing.nodeValue = "1";
								var Element = document.getElementsByTagName("table")[idx];
								Element.setAttributeNode(table_spacing);
								// Farbwechsel hellblau-dunkelblau
								var tr_list = tbl_obj.getElementsByTagName('tr');
                  for( var cnt=1; cnt<tr_list.length; cnt++ )
                    tr_list[cnt].className = (cnt % 2) ? odd_class : even_class;
              }  // if table_obj ...
        }  // for idx ...
  }  // matrix_table()


//----------------------------------------------------------------------------
function g_button( label, _href, color, /* optional */ _target )
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 06.11.2007 12:45 / Andreas Christ
//              Aufbau nach Anchor-in-Table geaendert.
// @date    : 06.11.2007 12:45 / Andreas Christ
//              Farbauswahl fuer spotlight.
// @date    : 31.10.2007 14:02 / Andreas Christ
//              Farbangabe.
// @date    : 24.10.2007 18:21 / Andreas Christ
//              Produktiv.
// @date    : 23.10.2007 13:42 / Andreas Christ
//              Pilot.
// @brief   : Erzeugt einen grafischen Button an der aktuellen Position im
//            HTML-Code
//
//              <script language="javascript" type="text/javascript">
//                g_button( 'Buttontext', 'http://irgendwohin' );
//              </script>
//            aktiviert werden.
// @param   : label   -> Text der Buttonbeschriftung
// @param   : _href   -> URL oder #
// @param   : color   -> Farbe des Hintergrunds (white|gray|grey|spot)
// @param   : _target -> Optionalparameter, gibt Linkziel bei Frames an
//
// @return  : -none-
//----------------------------------------------------------------------------
  {
    var img_left, img_body, img_right;

    // select button-background
    switch( color.toLowerCase() )
      {
      case 'white'  : img_left  = '/~sapidp/011000358700001034212007E/bttn_left_white.png';
                      img_body  = '/~sapidp/011000358700001034212007E/bttn_body_white.png';
                      img_right = '/~sapidp/011000358700001034212007E/bttn_right_white.png';
                      break;

      case 'spot'   : img_left  = '/~sapidp/011000358700001034212007E/bttn_left_spot.png';
                      img_body  = '/~sapidp/011000358700001034212007E/bttn_body_spot.png';
                      img_right = '/~sapidp/011000358700001034212007E/bttn_right_spot.png';
                      break;

      case 'grey'   :
      case 'gray'   :
      default       : img_left  = '/~sapidp/011000358700001034212007E/bttn_left.png';
                      img_body  = '/~sapidp/011000358700001034212007E/bttn_body.png';
                      img_right = '/~sapidp/011000358700001034212007E/bttn_right.png';
                      break;
      }  // switch color ...

    var url_left  = 'url(' + img_left + ')';
    var url_body  = 'url(' + img_body + ')';
    var url_right = 'url(' + img_right + ')';
    var code = new Array();

    var _targ = ( !_target ) ? '' : ' target="'+_target+'" ';

    var _link  = '<a class="g_button"';
        _link += '   href="' + _href + '"';
        _link += _targ;
        _link += '   onmouseover="g_button_hover(this, \'' + color.toLowerCase() + '\', 1);"';
        _link += '   onmouseout="g_button_hover(this, \'' + color.toLowerCase() + '\', 0);">';

    code.push( "\n" );
    code.push( '<!-- Button Start -->' );
    code.push( '<span class="g_button">' );
    code.push( '  <table>' );
    code.push( '    <tr>' );
    code.push( '      <td class="left" style="background-image: ' + url_left + ';"></td>' );
    code.push( '      <td class="body" style="background-image: ' + url_body + ';">' + _link + label + '</a></td>' );
    code.push( '      <td class="right" style="background-image: ' + url_right + ';"></td>' );
    code.push( '    </tr>' );
    code.push( '  </table>' );
    code.push( '</span>' );
    code.push( '<!-- Button End -->' );

    document.write( code.join("\n") );
  }  // g_button()



//----------------------------------------------------------------------------
function g_button_hover( obj, color, stat )
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 31.10.2007 15:15 / Andreas Christ
//              Farbangabe spot hinzugefuegt.
// @date    : 31.10.2007 15:15 / Andreas Christ
//              farbangabe.
// @date    : 24.10.2007 18:22 / Andreas Christ
//              Produktiv.
// @date    : 24.10.2007 13:26 / Andreas Christ
//              Pilot.
// @brief   : Erzeugt eine hover-Effekt über dem g_button
//            Es wird ein Anchor-Object übergeben, darunter wird nach der zu
//            veraendernden TD gesucht und diese veraendert.
// @param   : obj   -> Anchor-Object
// @param   : color -> Hintergrundfarbe des Buttons
// @param   : stat  -> 0 : hover off
//                     1 : hover on
//
// @return  : true  -> erfolgreich
//            false -> hat keine Childs
//----------------------------------------------------------------------------
  {
    var _url = '';
    var _url_hover = '';

      while(  obj.tagName != 'SPAN' )  // search parent-SPAN
        obj = obj.parentNode;

      if( obj.hasChildNodes() != true )
        {
          return( false );  // leider kinderlos
        }

    // select button-background
    switch( color.toLowerCase() )
      {
      case 'white'  : _url        = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left_white.png)';
                      _url_hover  = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left_white_reverse.png)';
                      break;

      case 'spot'   : _url        = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left_spot.png)';
                      _url_hover  = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left_spot_reverse.png)';
                      break;

      case 'grey'   :
      case 'gray'   :
      default       : _url        = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left.png)';
                      _url_hover  = 'url(https://websmp209.sap-ag.de/~sapidp/011000358700001034212007E/bttn_left_reverse.png)';
                      break;
      }  // switch color ...

    var classname = 'left';  // nach diesem classname wird gesucht
    var bg_image = ( stat == 0 ) ? _url
                                 : _url_hover;

    var td_list = obj.getElementsByTagName('td');
    var td_obj = null;
      for( var idx=0; idx<td_list.length; idx++ )
        {
          var td_obj = td_list[idx];
          if( td_obj.className == classname )
            {
              td_obj.style.background = bg_image;
              break;
            }
        }  //for idx ...
  }  // g_button_hover()


//----------------------------------------------------------------------------
function back_to_top( label )
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 07.11.2007 15:43 / Andreas Christ
//              Anpassungen fuer ~sapidb.
// @date    : 30.10.2007 21:55 / Andreas Christ
//              Pilot.
// @brief   : Erzeugt einen Link "Back to top"-Button an der aktuellen Position
//            im HTML-Code
//
//              <script language="javascript" type="text/javascript">
//                back_to_top( 'Back to top' );
//              </script>
//
// @param   : label -> Text des Links
//
// @return  : -none-
//----------------------------------------------------------------------------
  {
    var code = new Array();

    code.push( "\n" );
    code.push( '<!-- Back to Top  Start -->' );
    code.push( '<span class="back_to_top">' );
    code.push( '  <img src="https://websmp209.sap-ag.de/~sapidp/011000358700001035912007E/back_to_top.jpg"' );
    code.push( '       alt="Back to top"' );
    code.push( '       title="Back to top"' );
    code.push( '       onclick="location.href=\'#document_top\';"' );
    code.push( '       onmouseover="this.src=\'https://websmp209.sap-ag.de/~sapidp/011000358700001035912007E/back_to_top_reverse.jpg\';"' );
    code.push( '       onmouseout="this.src=\'https://websmp209.sap-ag.de/~sapidp/011000358700001035912007E/back_to_top.jpg\';">' );
    code.push( '  <a href="#document_top"> ' + label + ' </a>' );
    code.push( '</span>' );
    code.push( '<!-- Back to Top End -->' );

    document.write( code.join("\n") );
  }  // back_to_top()



//==============================================================================
// Functions for the functionbar
//==============================================================================

//----------------------------------------------------------------------------
function chg_anchor_img( obj, newimg )
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 26.10.2007 17:11 / Andreas Christ
//              Pilot.
// @brief   : Tauscht das aktuelle Bild gegen newimg.
// @param   : obj     -> Anchor-Object
// @param   : newimg  -> neues Image
//
// @return  : true  -> erfolgreich
//            false -> hat keine Childs
//----------------------------------------------------------------------------
  {
      if( obj.hasChildNodes() != true )
        {
          return( false );  // leider kinderlos
        }

    var img_list = obj.getElementsByTagName('img');
    img_list[0].src = newimg;
  }  // chg_anchor_img()



//==============================================================================
// Functions for quicksearch
//==============================================================================

//----------------------------------------------------------------------------
function chg_srch_img( obj, newimg )
//----------------------------------------------------------------------------
// @author  : Andreas Christ
// @date    : 08.11.2007 22:49 / Andreas Christ
//              Pilot.
// @brief   : Tauscht das aktuelle Bild gegen newimg.
// @param   : obj     -> Object
// @param   : newimg  -> neues Image
//
// @return  : true  -> erfolgreich
//            false -> hat keine Childs
//----------------------------------------------------------------------------
  {
    var papa = obj.parentNode;
    var tag_list = papa.getElementsByTagName('input');

      for( var idx=0; idx<tag_list.length; idx++ )
        {
          if( tag_list[idx].type == 'image' )
            {
              tag_list[idx].src = newimg;
              break;
            }
        }  // for ...
  }  // chg_srch_img()


//==============================================================================
// Functions misc
//==============================================================================


//----------------------------------------------------------------------------
function expand_height( div_id, _offset )
//----------------------------------------------------------------------------
// @author : Andreas Christ
// @date : 30.10.2009 11:08 / Andreas Christ
// Anpassungen fuer ie8.
// @date : 11.12.2007 12:32 / Andreas Christ
// Pilot.
// @brief : Expand the height of the content.
// @param : div_id -> ID of the content-main DIV.
// @param : _offset -> offset of the upper div in px.
//
// @return : -none-
//----------------------------------------------------------------------------
{
var view_height = 0;
var div_height = 0;
var div_obj = document.getElementById( div_id );
if ( typeof(document.body.clientHeight) != 'undefined' )
{
view_height = document.documentElement.clientHeight;
div_height = div_obj.clientHeight
}
else
{
view_height = window.innerHeight;
div_height = div_obj.offsetHeight;
}

var num_height = Math.max( parseInt( div_height ), parseInt(
view_height ) - _offset );
var _height = num_height.toString(10) + 'px';
div_obj.style.height = _height;
}
// expand_height()



//----------------------------------------------------------------------------
function email_us()
//----------------------------------------------------------------------------
// @author  : Andreas Mohr
// @date    : 18.06.2009 Andreas Mohr
// @brief   : Erstellt die Email-Us-Box aufgrund von H1 und H2 im content-header
//----------------------------------------------------------------------------
  {	
		var code = new Array();
		code.push("<p><img src='https://websmp209.sap-ag.de/~sapidp/011000358700000712142007E.gif' alt='' width=18 height=13 hspace=5 border=0 align=absmiddle>");
		/* Subject is H2 of content-header. If H2 of content-header is not existing or empty, then H1 will be selected as subject */
		if (!document.getElementsByTagName('h2')[0] || document.getElementsByTagName('h2')[0].innerHTML == "") {
			subject = document.getElementsByTagName('h1')[0].innerHTML; }
		else if (document.getElementsByTagName('h2')[0].innerHTML != "") {
			subject = document.getElementsByTagName('h2')[0].innerHTML; }
		code.push('<a href=\"mailto:' + mail);
		if (cc != "") {
			code.push('?cc=' + cc);
			}
		code.push('&subject=' + subject  + '\">');
		code.push("Email us</a></p>");
		document.write( code.join("\n") );
	}

// Grab omni.epx
var ROIL_script = document.createElement('script');
ROIL_script.type = "text/javascript";
ROIL_script.src = "https://www.sap.com/omni.epx";
document.getElementsByTagName('head')[0].appendChild(ROIL_script);

// Global file
var ROIL_global_script = document.createElement('script');
ROIL_global_script.type = "text/javascript";
ROIL_global_script.src = "https://www.sap.com/global/tracking/js/s_code.js";
document.getElementsByTagName('head')[0].appendChild(ROIL_global_script);

/*
 * onDOMReady
 * Copyright (c) 2009 Ryan Morr (ryanmorr.com)
 * edited by PCL for ROI Labs
 * Licensed under the MIT license.
 */
 
function onDOMReady(fn,onload,ctx){var r,t;var onStateChange=function(e){if(e&&e.type=="DOMContentLoaded"){fireDOMReady();}else if(e&&e.type=="load"){fireDOMReady();}else if(document.readyState){if((/loaded|complete/).test(document.readyState)){fireDOMReady();}else if(!!document.documentElement.doScroll){try{ready||document.documentElement.doScroll('left');}catch(e){return;}
fireDOMReady();}}};var fireDOMReady=function(){if(!r){r=true;if(onload){if(window.addEventListener){window.addEventListener('load',window.onload,false);}else if(window.attachEvent){window.attachEvent('onload',window.onload);}}
fn.call(ctx||window);if(document.removeEventListener)
document.removeEventListener("DOMContentLoaded",onStateChange,false);document.onreadystatechange=null;window.onload=null;clearInterval(t);t=null;}};if(document.addEventListener)
document.addEventListener("DOMContentLoaded",onStateChange,false);document.onreadystatechange=onStateChange;t=setInterval(onStateChange,5);window.onload=onStateChange;};

    var s_account = (typeof(window.s_account)=='string')?window.s_account:'sappartner,sapglobal';
    /************************** CONFIG SECTION **************************/
    /* Get page/server info */
        var sap = new Object();
        sap.lastFileUpdate = '2012.04.03'; // DS
        sap.linkInternalFilters = 'intranet.sap.com,service.sap.com,sap.corp,sap-ag.de';
        sap.thisHost=(typeof(s_devHost)!='undefined')?s_devHost:window.location.host.toLowerCase();
        sap.thisPathName=(typeof(s_devPath)!='undefined')?s_devPath:window.location.pathname.toLowerCase();
        sap.thisSearch=(typeof(s_devSearch)!='undefined')?s_devSearch:window.location.search.toLowerCase();
        sap.thisProtocol=window.location.protocol.toLowerCase();
        sap.thisParentPath=(typeof(s_devParentPath)!='undefined')?s_devParentPath:(typeof(window.top.location)!='undefined' && window.top.location!=window.location)?window.top.location.pathname:false;


    // Local download handler filter
    function s_checkLocalDownloadHandler(url) {
        return url;
    }
    // Return the unique Object ID out of a URL.
    function s_pageObject(url) {
        return url.match(/\/?([0-9]{24,25}[A-Z]?)\/?/);
    }
    
    function local_s( ) { 
        /* Default Site Variables */
        s.server    = 'partner';
        s.prop1     = 'gl';
        s.prop2     = 'us-en';
        s.prop5     = 'glo';
        s.prop9     = 'logY';
        s.linkDownloadFileTypes = 'exe,zip,wav,mp3,mov,mpg,avi,wmv,pdf,doc,docx,xls,xlsx,ppt,pptx,rar,sda';

        
        var countryPortalRegex = new RegExp(/\/?partnerportal\/(home|germany|japan)/i);
        if(sap.thisParentPath!=false && sap.thisParentPath.match(countryPortalRegex)) {
            var tempParent = sap.thisParentPath.match(countryPortalRegex);
            if(tempParent[1]) {
                switch(tempParent[1].toLowerCase()) {
                    case 'germany':
                        s.prop5 = 'de';
                        s.prop1 = 'emea';
                    break;
                    case 'japan':
                        s.prop5 = 'jp';
                        s.prop1 = 'apj';
                    break;
                    case 'home':
                    default:
                        s.prop5 = 'glo';
                        s.prop1 = 'gl';
                    break;
                }
            }
        }

        sap.hier = sap.thisPathName.replace(sap.thisSearch,'').replace(/^\//,'').replace(/^~[A-Z]+/i,'');
        sap.uniquePage = s_pageObject(sap.hier);
        if(sap.uniquePage!=null && sap.uniquePage[1]!=null) 
            sap.uniquePage = sap.uniquePage[1].replace(/\//g,'');
        else 
            sap.uniquePage = 'folder';

        // set s object hierarchy variables
        if(!s.pageName || s.pageName=='') 
            s.pageName = s.server+':'+s.prop5+':'+sap.uniquePage+':'+document.title.toLowerCase();
        
        if(!s.hier1)
            s.hier1 = s.server+','+s.prop1+','+s.prop5+','+sap.uniquePage+','+document.title.toLowerCase();
            
        if(!s.channel)
            s.channel = 'partner';
            
        if(sap.thisPathName.replace(sap.thisSearch,'').match(/\/~sapidp\//)) 
            s.prop9 =   'logN';
            
        if(s.getPreviousValue(s.prop9,'c9') == 'logN' && s.prop9 == 'logY') 
            s.events=s.apl(s.events,'event8',',',2);
            
        /* internal search */
        if(typeof ROIL_searchTerm == 'string'){
            s.prop11=ROIL_searchTerm.toLowerCase();
        }
        if(typeof ROIL_searchEmpty == 'string'){
            s.prop12 = s.prop11;
        }
    }

onDOMReady(function() {
    function waitingForOmn(omnRetryCount) 
    {
        if(typeof s === 'object')
        {
            try { var s_code=s.t(); if(s_code)document.write(s_code); } catch (e) { }
            return true;    
        }
        
        if(omnRetryCount == 10)
        {
            return false;
        }
        
        omnRetryCount++;
        window.setTimeout(function() { waitingForOmn(omnRetryCount); }, 500);
    }
    waitingForOmn(1);   
}, true);