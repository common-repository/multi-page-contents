<?php
/*  
Plugin Name:  Multi-Page content (Chapters)
Description: Make a single post/page and divide it in Multiple pages, like a chaptered book.  (P.S.  OTHER MUST-HAVE PLUGINS FOR EVERYONE: http://bitly.com/MWPLUGINS  )
Version: 1.22
LICENCE: Free
Author: TazoTodua
Author URI: http://www.protectpages.com/profile
Plugin URI: http://www.protectpages.com/
Donate link: http://paypal.me/tazotodua
*/
define('version__MPCC', 1.22);		if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly
define('TableName1__MPCC',		$GLOBALS['wpdb']->prefix .'multipage_contents_bakcup');
define('NextPageReg__MPCC',		'<!--nextpage-->');
define('Devider__MPCC',			'<mppc_exloder />');
define('Pregex1__MPCC',			NextPageReg__MPCC);
define('TitleStart__MPCC',		'<mpcc_titlee>'); 
define('TitleEnd__MPCC',		'</mpcc_titlee>');
define('TitleStartRegex__MPCC',	'\<mpcc_titlee\>');
define('TitleEndRegex__MPCC',	'\<\/mpcc_titlee\>');
define('ErrorMessage1__MPCC',	'There is a problem..Probably, in plugin functionality , or with Current Wordpress Version.please,contact plugin developer (link is in bottom), and provide your wordpress verion/details to understand and solve the problem.');
define('ContactMeUrl__MPCC',	'http://j.mp/contactmewordpresspluginstt');
define('pluginpage__MPCC',		'my-mpcc-slug');
define('plugin_settings_page__MPCC', 	 admin_url( 'options-general.php'). '?page='.pluginpage__MPCC  );

									
								

add_action('admin_menu','myf452__MPCC');function myf452__MPCC(){ add_submenu_page('options-general.php','Multi-Page content','Multi-Page content', 'manage_options' , pluginpage__MPCC, 'mpcc__callback' );} function mpcc__callback(){
	
	if(isset($_GET['isactivation'])) { echo '<script>alert("If you are using multi-site, you should set these options per sub-site one-by-one");</script>'; }
	?> 
	<style>span.codee{background-color:#D2CFCF; padding:1px 3px; border:1px solid; font-family: Consolas;} </style>
	<div class="eachLine" style="margin: 40px 0 0 0;"><br/>
		* You can see the metaboxes under the PAGE/POST editor.   (  Note, the plugin uses &lt;!--nextpage--&gt; tag.  If you dont know what I am talking about, then forget it. ) 
		<br/>* To style/design the blocks&links&output of plugin, use default CSS hooks.  
		<br/>* To change the default phrase <b>"TABLE OF CONTENTS"</b>, use php filter, example:  
		<br/><span class="codee">add_filter('TOCtitle__MPCC','your_funct'); function your_funct($content){  return "Here is my Pagess:";  }</span>
		<br/><br/><br/><br/><br/>* In case of problems, please, <a href="<?php echo ContactMeUrl__MPCC;?>" target="_blank"> contact me </a>.
		
	</div>
	<?php 
}


//ACTIVATION HOOK
register_activation_hook( __FILE__, 'activation__MPCC' );function activation__MPCC() { 	global $wpdb;
		$InitialArray = array( 
			//'MPCC__smth'				=> '1',
			);
		foreach($InitialArray as $name=>$value){	if (!get_option($name)){update_option($name,$value);}	}
	
		// Essentials
		$MustHaveArray = array( 
			'optMPCC__version'				=> version__MPCC,
			);
		foreach($MustHaveArray as $name=>$value){ update_option($name,$value);}	
		
	//create table
			$bla55555 = $wpdb->get_results("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'");
			$engine = ''; //'ENGINE='. ( !empty($bla55555[0]->SUPPORT) ? 'InnoDB' : 'MyISAM'  );
	$InnoDB_or_MyISAM = ($bla55555[0]->SUPPORT) ? 'InnoDB' : 'MyISAM' ;
	$x= $wpdb->query("CREATE TABLE IF NOT EXISTS `".TableName1__MPCC."` (
		`IDD` int(30) NOT NULL AUTO_INCREMENT,
		`lang` varchar(170) CHARACTER SET utf8 NOT NULL,
		`postID` int(50) NOT NULL,
		`part` int(50) NOT NULL,
		`content` longtext CHARACTER SET utf8 NOT NULL,
		`partTITLE` text CHARACTER SET utf8 NOT NULL,
		`PartsAmount` int(100) NOT NULL,
		`Extr1` varchar(100) NOT NULL,
		PRIMARY KEY (`IDD`),
		UNIQUE KEY `IDD` (`IDD`)
		) ".$engine."  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;"
	);
}

add_action('admin_init','startt_func__MPCC');
function startt_func__MPCC(){ 
 if (is_admin()){	require_once(ABSPATH . 'wp-includes/pluggable.php'); $usID= get_current_user_id();  global $pagenow;
   if (	(in_array( $pagenow, array('post.php')) )	//if Edit page 
			||	(in_array( $pagenow, array('post-new.php'))) 						//if NEW page
   ){
	add_filter( 'the_editor_content', 'MultipageFilter__MPCC',7,2);
	function MultipageFilter__MPCC($content,$part=1){
		$exploded=explode(Pregex1__MPCC,$content);
		if (count($exploded) > 1){ 
			foreach ($exploded as $name=>$value){ $GLOBALS['mpcc_contents'][$name] = $value; }
			return $exploded[$part-1]; //minus 1 is because, array starts from 0
		}
		return $content;
	}
	
	add_action( 'add_meta_boxes', 'mtbx_1__MPCC' );	function mtbx_1__MPCC() { 	foreach (get_post_types() as $each) {add_meta_box('Id_44__MPCC', 'Multi-Page contents' ,'aBox1__MPCC', $each );	}	}	
	function aBox1__MPCC( $post ){	
		global $wpdb; 
		$postIdd=(!empty($_GET['post'])) ?  $_GET['post'] : $post->ID;
		$indxArray= SanitizedIndxArray__MPCC($postIdd); 
		$chaptingEnabled=  "m_enabled" == $indxArray['EnbDsb'] ;  //support for older versions
		
		$ThisPostCh= GetPostChapt__MPCC($post->ID,1);
		$Post_title = !empty($ThisPostCh[0]) && !empty($ThisPostCh[0]->partTITLE) ? $ThisPostCh[0]->partTITLE : '';
		?>
	<div class="MPCC_AREA">
		<style>
		xxbody #post-body.columns-2 #postbox-container-1 {position:fixed;margin-right:0px; right:10px;top:20px;}
		a.savetxtbut{background-color: green;border: 2px solid currentColor;border-radius: 5px;color: white;left: 300px; padding: 10px; position: relative;top: -40px;}	a.savetxtbut:hover{font-size:1.1em;}
		#loaderrr{display: block;min-height: 100px;min-width: 350px;padding: 15px;margin: 0px 10% 10% 10%;position: fixed;top: 5%;overflow:hidden;z-index: 35;}
		a.addChapterClass{background-color:red; border:2px solid; border-radius:5px;color:white; padding:5px; position:relative; margin:0 10px;}
		input.titlcls{width:50%;}
		.mpBOOKtitle{text-align:center; margin:20px 0 5px 0;}
		#Id_44__MPCC{background-color: #2CEA2C;}
		.chNUMB{font-size:2em;line-height: 1em;}
		.EACHb_MPCC{background-color:#FFF6CE; margin:20px 0;padding:3px 10px;}
		.Opacity__mpcc{opacity:0.3;}
		.Opacity_FULL__mpcc{opacity:1;}
		#title1__MPCC{width:100%;}
		#AfterPgtitl{background-color:#2CEA2C; padding:5px; margin:10px 0;}
		.Enable_Chaps{background-color:rgb(255, 255, 255); padding:3px;  display:inline-block; margin:0 0 10px 0;}
		</style>
		
		<div id="AfterPgtitl">
		  <div class="Enable_Chaps">
			 Enable Chapters:  <input type="checkbox" id="chp_enabler"  autocomplete="off" onchange="Opacity_changerr();" value="ok"  <?php echo ( $chaptingEnabled ? 'checked="checked"': '' );?> />
		  </div>
		  <script type="text/javascript">
			function Opacity_changerr(){ 
				MyElms=document.getElementsByClassName("Opacity__mpcc");
				if (document.getElementById("chp_enabler").checked)
					 { for (var i=0; i < MyElms.length; i++)  {MyElms[i].className += "  Opacity_FULL__mpcc"; } }
				else { for (var i=0; i < MyElms.length; i++)  {MyElms[i].className = MyElms[i].className.replace("Opacity_FULL__mpcc","").trim(); }		}
			}
		  </script>
		
		
		  <div class="Opacity__mpcc <?php echo ($chaptingEnabled? "Opacity_FULL__mpcc":"");?> ">
			(<a href="javascript:alert('If enabled chapters, then in this field you should insert the title  of introduction chapter (introduction chapter should be inserted in main text editor window)');">Read this popup!</a>):  
			<input type="text" name="title1__MPCC" id="title1__MPCC" value="<?php echo stripslashes($Post_title);?>"  placeholder="Title for Introduction Chapter...  "  />
		  </div>
		 <!-- <div style="clear:both;width:100%;height:20px;background:black;margin:0 0 40px 0;"></div> -->
		</div>
				<script type="text/javascript">
				//insert this block below title
				var the_div = document.getElementById("AfterPgtitl"); var target_div= document.getElementById("postdivrich"); 	target_div.insertBefore(the_div, target_div.childNodes[0]);
				</script>
		
		
		<div class="Opacity__mpcc <?php echo ($chaptingEnabled? "Opacity_FULL__mpcc":"");?> ">
			<input type="hidden" name="chps_amount__MPCC" id="chps_amount__MPCC" value="<?php echo $indxArray['ChapAmount'];?>"  autocomplete="off"  />						
			<input name="mpBOOK_UPDATION" value="ok" type="hidden" />
			<div id="BookPContainer">
				<?php for ($i=2; $i<=$indxArray['ChapAmount']; $i++ ) { echo '<div id="MPCC_b_'.$i.'" class="EACHb_MPCC">'; output_MPbook_editor__MPCC($postIdd,$i); echo '</div>';  } ;?>
			</div>
			<div class="CH_buttons">
			  <a href="javascript:void(0);" onclick="RemoveLastChap__mpcc();"  class="addChapterClass" style="font-style:italic;">(Remove Last Chapter)</a>
			  <a href="javascript:void(0);" onclick="AddNewChap__mpcc();"  class="addChapterClass" style="background-color:#6900FF">Add CHapter</a>
			</div>
			<div style="clear:both;"></div>
			<a class="contact_author" style="padding:5px 10px; background-color:#CBA70D;position:absolute;bottom:2px; right:10px;" href="<?php echo ContactMeUrl__MPCC;?>" target="_blank">Contact Plugin Author.</a>
		</div>
		<script type="text/javascript">
		var postid__MPCC=document.getElementById("post_ID").value;
		var postlang__MPCC=postlang__MPCC || "";
		var ErrorMessage= "<?php echo ErrorMessage1__MPCC;?>";
		START__MPCC=false; Array__mpcc =[];
		
		
			function check_if_plugin_enabled()	{  return document.getElementById("chp_enabler").checked; }
			function check_if_ERROR_MSG()		{  if (!check_if_plugin_enabled()) {alert("At first, check the initial checkbox to enable chapters"); return false;}  return true; }
					
		function AddNewChap__mpcc(){			if (!check_if_ERROR_MSG()) return false;
		  var LastChapter=document.getElementById('chps_amount__MPCC');				
		  ChapNumber__MPCC=parseInt(parseInt(LastChapter.value))+1;			
		  LastChapter.value = ChapNumber__MPCC;
		  textarea_IDD = 'mp_TXTid_'+ChapNumber__MPCC;
		  PLEASE_WAIT_Box(true, "Loading..");
			jQuery.post(ajaxurl,  { 
				action: "Output_wpEDITOR", 
				pid: postid__MPCC ,
				lChap: ChapNumber__MPCC, 
			  },
			  function(response,status){ My_Exec_Tinymce(response,status); }
			);
		}
		
		function My_Exec_Tinymce(response,status){
			PLEASE_WAIT_Box(false);
			if(status =! "success") {alert("cant_ajax_error245_"+response); return false;}
			
			document.getElementById('BookPContainer').insertAdjacentHTML('beforeend','<div id="MPCC_b_'+ChapNumber__MPCC+'" class="EACHb_MPCC">'+response+'</div>'); 
			
			Copy_tinyMCEPreInit = tinyMCEPreInit;  //Copy_tinyMCEPreInit.mceInit.content.selector= textarea_IDD;
			tinymce.init(Copy_tinyMCEPreInit.mceInit.content); 
			tinyMCE.execCommand('mceAddEditor', false, textarea_IDD);   
			quicktags({id : textarea_IDD});
			
			window.setTimeout(function(){var x=document.getElementById('chNUMB_'+ChapNumber__MPCC); x.style.fontSize='3em';x.style.color='red';}, 1000);
		}
		
		
		
		function RemoveLastChap__mpcc(){		if (!check_if_ERROR_MSG()) return false;
		  var LastChapter=document.getElementById('chps_amount__MPCC'); 
		  var ChapElem=(document.getElementById("MPCC_b_"+LastChapter.value) ?  document.getElementById("MPCC_b_"+LastChapter.value) : false);
		  if(ChapElem){ChapElem.parentNode.removeChild(ChapElem);}
		  DecreasedChap__MPCC=parseInt(parseInt(LastChapter.value) - 1);    LastChapter.value = (DecreasedChap__MPCC > 1 ) ? DecreasedChap__MPCC : 1;
		  alert("removed");
		}
		
		
		//Click handler - you might have to bind this click event another way
		//jQuery('input#publish, input#save-post').click(function(e){  SubmCLICKED(e);	});
		Submit_Btn__MPCC = (document.getElementById("publish") ? document.getElementById("publish")  : document.getElementById("save-post") || false );
		if (Submit_Btn__MPCC)	{Submit_Btn__MPCC.addEventListener("click", SubmCLICKED, false);}
		function SubmCLICKED(e){
		  if (!START__MPCC && check_if_plugin_enabled()) {
			Array__mpcc =[];
			PLEASE_WAIT_Box(true, "Please wait. Chapters are being saved...");
			var LastChap=document.getElementById('chps_amount__MPCC').value;	 	  
			if (LastChap > 1) { 
				Array__mpcc.push({ name:'action',			value:"SAVE_Book_MPPC" });								//admin-ajax name
				Array__mpcc.push({ name:'ChaptingEnabled',	value:(check_if_plugin_enabled()? "m_enabled":"m_disabled")  });	//Chapting is Enabled
				Array__mpcc.push({ name:'ChaptersAmount',	value:LastChap}); 										//Chapter Amount
				Array__mpcc.push({ name:'PostId', 			value:postid__MPCC});									//MainPost ID	
				Array__mpcc.push({ name:'PostTitl',			value:document.getElementById("title").value });		//MainPost title
				Array__mpcc.push({ name:'PostHtitle', 		value:document.getElementById("title1__MPCC").value});	//MainPost H-title
				Array__mpcc.push({ name:'PostCont',			value:getContentt("content") });						//MainPost Content
				Array__mpcc.push({ name:'PostLang', 		value:postlang__MPCC});									//MainPost Lang
				
				for (var i=2; i<= LastChap; i++){
					Array__mpcc.push(	{name: 'titlee_'+i,		value: document.getElementById("chtitle_"+i).value} );
					Array__mpcc.push(	{name: "contentt_"+i,	value: getContentt("mp_TXTid_"+i)} ); 
				}
				//'./index.php?mp_action=SaveMPbook' 
				jQuery.post(ajaxurl,     Array__mpcc,     function(response,status){
					PLEASE_WAIT_Box(false);
					if(status == "success") {
						if(response!="success_MPCC"){alert("\r\n\r\nERRorr_MSG:"+response); return false;}
						else { START__MPCC=true; Submit_Btn__MPCC.click(); }
					}
				});
				e.preventDefault(); return false; 
			}
		  }
		}
				function getContentt(el_id){
					//Detect Type of Textarea
					var txt_Container= document.getElementById("wp-"+el_id+"-wrap");
					if (txt_Container.className.indexOf("tmce-active") > -1)		{ var areaType='tinymcee';	var cnt=tinyMCE.get(el_id).getContent();}
					else if (txt_Container.className.indexOf("html-active") > -1)	{ var areaType='htmll';		var cnt=document.getElementById(el_id).value;}
					else { var areaType='unknownn'; var cnt='content_not_found__err#522'; alert("err232__ Cant get chapterID:"+el_id+";  \r\n\r\n" + ErrorMessage); }
					// detect textarea type
					Array__mpcc.push({ name: "AreaTypee_"+el_id, 		value:areaType});
					return cnt;
				}
				
		</script>
		<script type="text/javascript">
		//############### "PLEASE WAIT" popup   ################ https://github.com/tazotodua/useful-javascript/ ################
		function PLEASE_WAIT_Box(Show_or_Hide, Loading_Message){
			if(Show_or_Hide){ 
			 var z = document.createElement("div"); z.id = "my_waiting_box_888";  z.innerHTML=  '<div style="background-color:black; color:white; opacity:0.9;height:8000px; left:0px;  position:fixed; top:0px; width:100%; z-index:1007;" id="ppa_shadow"> <div style="position:absolute; top:200px;left:49%; z-index: 1008;" id="ppa_load">'+ ( Loading_Message || '<span id="ppa_phrase" style="color:grey;font-size:24px;">LOADING...</span>')+'<br/></div></div>'; document.body.appendChild(z); 
			}
			else { var x=document.getElementById("my_waiting_box_888"); x.parentNode.removeChild(x); }
		}
		//########################################################################################################################
		</script>
	</div>
	<?php 
	} // # if post-edit
  }// # metabox
 }
}

//================= generic functions ================//
function GetPostChapt__MPCC($postid,$part=false){ global $wpdb; 
	if(!is_numeric($postid)) {exit("incorect_postid:".$postid);}
	if($part) { if (!is_numeric($part)) {exit("incorect_part:".$part);}  $part = (int) $part;  }
	return $GLOBALS['wpdb']->get_results("SELECT * FROM ".TableName1__MPCC." WHERE postID = '". (int) $postid."'".   ($part ? " AND part = '". (int) $part ."'" : "" ) );
}
function SanitizedIndxArray__MPCC($post_id){ 
	$a= GetPostChapt__MPCC($post_id,0);
	if (!empty($a[0]))	{ $new['ChapAmount']=$a[0]->PartsAmount;	$new['EnbDsb']=$a[0]->Extr1;  }
	else				{ $new['ChapAmount']=1;  					$new['EnbDsb']="m_disabled";  }
	return $new;
}


function get_chapters__MPCC($txt){	$exploded=explode(Pregex1__MPCC,$txt); return array('chapters'=>$exploded, 'chap_amount'=> count($exploded) ); }
function RemoveTitlePart__MPCC($contents){	return preg_replace('/'.TitleStartRegex__MPCC.'(.*?)'.TitleEndRegex__MPCC.'/si','',$contents); 	} 
function UPDATEE_OR_INSERTTT__MPCC($tablename, $NewArray, $WhereArray){	global $wpdb; $arrayNames= array_keys($WhereArray);
	//convert array to STRING
	$o=''; $i=1; foreach ($WhereArray as $key=>$value){ $value= is_numeric($value) ? $value : "'".addslashes($value)."'"; $o .= $key . " = $value"; if ($i != count($WhereArray)) { $o .=' AND '; $i++;}  }
	//check if already exist
	$CheckIfExists = $wpdb->get_var("SELECT postID FROM $tablename WHERE $o");
	if (!empty($CheckIfExists))	{	$wpdb->update($tablename,	$NewArray,	$WhereArray	);}
	else						{	$wpdb->insert($tablename, 	array_merge($NewArray, $WhereArray)	);	}
}


//================= specific functions ================//
add_action('wp_ajax_Output_wpEDITOR','Ajax_wpeditor__MPCC',1);  function Ajax_wpeditor__MPCC(){
	output_MPbook_editor__MPCC($_POST['pid'], $_POST['lChap']) ; exit;
}
// ======================================= SHOW TEXT EDITOR ===================================== //
function output_MPbook_editor__MPCC($postid, $numb){ global $wpdb;
	$currentPart = GetPostChapt__MPCC($postid, $numb);
	if ($currentPart)	{ 
		$cTITLE= stripslashes($currentPart[0]->partTITLE);
		$cCONTENT= $currentPart[0]->content;
	}
	else	{$post=get_post($postid);$exploded=get_chapters__MPCC($post->post_content);
		$cTITLE = '';
		$cCONTENT=!empty($exploded['chapters'][$numb-1]) ? $exploded['chapters'][$numb-1] : 'default text';
	}
	$cCONTENT=RemoveTitlePart__MPCC($cCONTENT);  	echo '
	<div id="EachMP_block_'.$numb.'" class="MPCC_Ec ">
		<div class="mpBOOKtitle"><div class="chNUMB" id="chNUMB_'.$numb.'">(:'. ((int)($numb-1)).') </div> <input type="text" id="chtitle_'.$numb.'" value="'.$cTITLE.'" placeholder="Title for This Chapter" class="titlcls" /> </div>	<div style="clear:both;"></div>
		<div class="each_mpBOOK_textareaDIV"><div>';
			// NAME parameter dont needs to be set for TEXTAREA, because if we set it, on PUBLISH/UPDATE time, it is shtrown to $_POST session, and when the BOOK CONTENT is BIG, then $_POST cant handle such BIG DATA AMOUNT, and the page becomes problematic... so, we do it with AJAX request ... and REMOVED TEXTAREA
			//'<textarea class="each_mpBOOK_TXTAREA" id="boook_TE_'.$numb.'_1" name="mpBook_CONTENT__'.$numb.'" >'.$each_cont.'</textarea>';
			wp_editor( $cCONTENT , 'mp_TXTid_'.$numb, $settings = array(
			'editor_class'=>'each_mpBOOK_TXTAREA',    	/* 'textarea_name'=>'mpBook_CONTENT__'. $numb, */
			'tinymce'=>true ,'wpautop' =>false,	'media_buttons' => true ,	'teeny' => false, 'quicktags'=>true, ));
			echo '
		</div></div>	<div style="clear:both;"></div>
	</div>';	
}



// ======================================= SAVE action===================================== //
add_action('wp_ajax_SAVE_Book_MPPC', 'book_save_func__MPCC'); 
function book_save_func__MPCC(){ global $wpdb;

	$ChaptersAmount	=sanitize_key($_POST['ChaptersAmount']); 
	$PostId			=sanitize_key($_POST['PostId']);
	//$PostLang		=sanitize_key($_POST['PostLang']);
	if (CurrentUserCanEditThis__MPCC($PostId)){
	  //update INDEX record for post id
		UPDATEE_OR_INSERTTT__MPCC(TableName1__MPCC, array('PartsAmount'=>$ChaptersAmount, 'Extr1'=> sanitize_key($_POST['ChaptingEnabled']) ),	array('postID'=> $PostId, 'part'=> 0 ) );
	  //update the first (main content)
		$title= $_POST['PostHtitle']; 
		$pcont = stripslashes($_POST['PostCont']);
		$contnt=  ($_POST['AreaTypee_' . 'content']=='htmll'  ?  wpautop( $pcont, true)   :  $pcont) ;
		UPDATEE_OR_INSERTTT__MPCC(TableName1__MPCC, array('content'=>$contnt, 'partTITLE'=> $title),  array('postID'=> $PostId, 'part'=> 1) );
	  //update contents
	  for($i=2; $i <= $ChaptersAmount; $i++){
		$title= $_POST['titlee_'.$i]; 
		$pcont = stripslashes($_POST['contentt_'.$i]);
		$contn = ($_POST['AreaTypee_'. 'mp_TXTid_'.$i]=='htmll'  ?  wpautop($pcont, true) : $pcont  ) ; 
		$contn = TitleStart__MPCC.$title.TitleEnd__MPCC   .   str_ireplace(Pregex1__MPCC,'',$contn);
		UPDATEE_OR_INSERTTT__MPCC( TableName1__MPCC, 	array('content'=>$contn, 'partTITLE'=> $title),	array('postID'=> $PostId, 'part'=> $i) );
	  }
	  //delete previous revisions & drafts
	  	$del= $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_type = 'inherit' AND post_parent = '%d'",$PostId) ); 
	
	  exit("success_MPCC");
	}
	else{ 
	  exit("error3543__".ErrorMessage1__MPCC);
	}
}		
		//Re-used functions
		function CurrentUserCanEditThis__MPCC($postid){
			require_once(ABSPATH . 'wp-includes/pluggable.php');	global $wpdb,$current_user;
			$authorID = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM ".$wpdb->prefix."posts WHERE ID ='%d'",  $postid) );
			//If opening new post
			if (!$authorID) { 
				if (current_user_can('edit_posts')) {return true;}			}
			//If editing
			if ($authorID) { 
				if ($authorID == $current_user->ID || current_user_can('delete_others_posts')) { return true; } 	}	
			return false;
		}


add_action( 'save_post', 'savpst_62__MPCC',95);	
function savpst_62__MPCC( $post_id ){ if (isset($_POST['post_ID']) && $post_id==$_POST['post_ID']) {  global $wpdb;	$additional_content=''; 
	$PArrayy	= GetPostChapt__MPCC($post_id);
	$indxArray	= SanitizedIndxArray__MPCC($post_id);
	//if chapting is enabled, then add CHAPTERS into to post_content (because the content should be searchable in SEARCH query...)
	if ($indxArray['EnbDsb'] == "m_enabled"){
		for ($i=2; $i <= $indxArray['ChapAmount'] ; $i++){
			foreach($PArrayy as $key=>$name){   if($name->part == $i){ $additional_content .= Pregex1__MPCC. $name->content; }   } 
		}
		$pst=get_post($post_id); 
		UPDATEE_OR_INSERTTT__MPCC( $GLOBALS['wpdb']->prefix."posts",   array('post_content' => $pst->post_content.$additional_content), array('ID'=> $post_id)   );
	}
} }







// ================== CONTENT OUTPUT =================== //
	//	add_filter( 'wp_link_pages_link', 'filter_links__MPCC', 11,2 ); function pagelinks_filter__MPCC($link, $prev ){  ....  }
	//	https://core.trac.wordpress.org/browser/tags/4.2.2/src/wp-includes/post-template.php#L842

	//	add_filter(	'wp_link_pages_args','pagelinks_filter__MPCC',11,1);function pagelinks_filter__MPCC($parameters){
	//		$defaults = array( 'echo'=> 1,
	//			'before'	 => '<div class="pages__MPCC">' . __( '' ),		'after'		=> '</p>',
	//			'link_before'=> '<span class="PageNum__MPCC">',				'link_after'=> '</span>',
	//			'next_or_number'=> 'number',
	//			'separator'		=> ' ',
	//			'nextpagelink'	=> __( 'Next page' ),  	'previouspagelink'=> __( 'Previous page' ),
	//			'pagelink'		=> '%',
	//		);
	//		return $defaults;
	//	}
//disable it... we will do manuall OUTPUT	
add_filter(	'wp_link_pages_args','pagelinks_filter__MPCC',11,1);function pagelinks_filter__MPCC($parameters){   return  array( 'echo'=> 0 );  }
	//add_shortcode( 'index_mpcc', 'chapters_outp_func' );function chapters_outp_func($atts) { return get_table_of_contents();}
add_filter('the_content','output_toc__MPCC',88);	function output_toc__MPCC($content){ return get_table_of_contents($content); }
function get_table_of_contents($content){	global $wpdb,$post;  
  $currentPost = GetPostChapt__MPCC($post->ID); 
  if (!empty($currentPost[0])){
	foreach ($currentPost as $eachPart) {if (0==$eachPart->part){$chapAmnt=$eachPart->PartsAmount; $c_EnblDisb=   "m_enabled"==$eachPart->Extr1; }   if (1==$eachPart->part){$postHtitle=stripslashes($eachPart->partTITLE);}  }
	if (isset($chapAmnt) && $chapAmnt>1 && $c_EnblDisb){
		$InitQueryPg= get_query_var('page'); $QueriedPage=(empty($InitQueryPg)) ? 1 : $InitQueryPg;
		if ($QueriedPage==1) {  $content = TitleStart__MPCC.$postHtitle.TitleEnd__MPCC.$content; }
																							$TOCtitle = apply_filters('TOCtitle__MPCC', "");
		$content .= '<div class="TOC_list__MPCC">  <div class="toctitle__MPCC">'. ( !empty($TOCtitle)  ? $TOCtitle : "Table of Contents").'</div>';
		$pLink = get_permalink($post->ID); 	$HomeUrl = home_url();  $base_paged_link = _wp_link_page__modified_to_get_base($post,9777797777);
		foreach ($currentPost as $eachPart){ $partN= $eachPart->part; if ($partN >= 1) {
			$pTitle= stripslashes($eachPart->partTITLE);
			$content .= '<div class="row__MPCC mpccr_'.$partN.'" ><span class="urlE__MPCC">'.( $QueriedPage == $partN ? '<span class="currentActv__MPCC">'.$pTitle .'</span>': '<a class="pageA__MPCC" id="part'.$partN.'" href="'. str_ireplace(9777797777, $partN ,$base_paged_link) .'">'. $pTitle .'</a>') .'</span></div>';			
		  }
		}
		$content .= '</div>';	
	}
  } return $content;
}

//Style title
add_filter('the_content','style_title__MPCC',89);function style_title__MPCC($content){
	$content = preg_replace('/'.TitleStartRegex__MPCC.'(.*?)'.TitleEndRegex__MPCC.'/si','<h1 class="ptitle__MPCC">$1</h1>',$content); 	return $content;
}
add_action('wp_head','stylesheet_for__MPCC');function stylesheet_for__MPCC(){	echo '
  <style type="text/css">.______BY_____MPCC___PLUGIN______{}
  h1.ptitle__MPCC{text-align:center;font-size:1.3em; font-weight:bold;margin: 10px 0;}
  .TOC_list__MPCC{clear:both; text-align:left; margin: 0 0 0 10%; background-color:#F2E7BA; padding:4px;}  .toctitle__MPCC{font-weight: bold; text-align: center;}
  .row__MPCC{margin:10px 10px;}    .row__MPCC::before{content: "\25CF"; margin: 0 10px;}
  span.urlE__MPCC{ display: inline-block; width: 80%;}  span.currentActv__MPCC{color:grey;margin:0 0 0 3px; font-size: 1.4em;}  a.active__mpcc{color:black;cursor:default;}
  a.pageA__MPCC{font-size: 1.4em;background-color: #E7E7E7; padding: 2px 5px;  color:blue; border-radius: 3px;  width: 100%; display: inline-block; }
  </style>';
}

		//sourced from  http://wpseek.com/function/_wp_link_page/ and modified to recude Database calls by 90%
		function _wp_link_page__modified_to_get_base($post, $i ) {
			global $wp_rewrite;
			if ( 1 == $i ) { $url = get_permalink($post);  } 
			else {
				if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )    $url = add_query_arg( 'page', $i, get_permalink() );
				elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )        $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
				else    $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
			}
			return esc_url( $url );
		}
		
		
		
		
		
		
								//===========  links in Plugins list ==========//
								add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), function ( $links ) {   $links[] = '<a href="'.plugin_settings_page__MPCC.'">Settings</a>'; $links[] = '<a href="http://paypal.me/tazotodua">Donate</a>';  return $links; } );
								//REDIRECT SETTINGS PAGE (after activation)
								add_action( 'activated_plugin', function($plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { exit( wp_redirect( plugin_settings_page__MPCC.'&isactivation'  ) ); } } );
								
?>