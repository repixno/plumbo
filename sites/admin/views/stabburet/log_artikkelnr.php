<?php

must_login();
if($uid != 618012){
    must_be_admin();
}
is_popup(1);

if(!$mode){
	$mode = 1;
}

if($mode == 1){
	$table = table(
		tr(
			td(
				"Artikkelnr:"
			).
			td(
				input(
					"artnr"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Fra dato (yyyy-mm-dd)"
			).
			td(
				input(
					"fradato"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Til dato (yyyy-mm-dd)"
			).
			td(
				input(
					"tildato"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Submit"
			).
			td(
				input(
					"mode"
					,
					array("type" => "hidden", "value" => "2")
				).
				input(
					"sumbit"
					,
					array("type" => "submit", "value" => "Kjør statistikk")
				)
			)
		)
		,
		array("border" => "1", "cellpadding" => "3", "cellspacing" => "0")
	);

	$output = form(
		$table
		,
		array("method" => "post", "action" => "log_artikkelnr.php")
	).p().examples();

	echo eurofoto(
		$output
	);
}
if($mode == 2){
	$artnrs = explode(";",$artnr);
	$na = count($artnrs);
	$antall = array();
	for($a=0;$a<$na;$a++){
		set_time_limit(30);
		$data = sql_allExec("select
								kampanje_kode,antall
							from
								historie_ordrelinje, historie_ordre
							where
								historie_ordrelinje.ordrenr=historie_ordre.ordrenr
							and
								date(tidspunkt)>='$fradato'
							and
								date(tidspunkt)<'$tildato'
							and
								artikkelnr=".$artnrs[$a]."
							and antall>0
								order
							by kampanje_kode;");
		
		$n = count($data);
		for($i=0;$i<$n;$i++){
			$kode = $data[$i]["kampanje_kode"];
			if(!$kode){
				$kode = "EF-997";
			}
			if(!$antall[$kode]){
				$antall[$kode] = array();
			}
			if(!$antall[$kode][$artnrs[$a]]){
				$antall[$kode][$artnrs[$a]] = 0;
			}
			$antall[$kode][$artnrs[$a]] += $data[$i]["antall"];
		}
	}
	$keys = array_keys($antall);
	$nkeys = count($keys);
	$td1 = td("Portalkode");
	$td2 = td($artnr);
	for($i=0;$i<$nkeys;$i++){
		$td1 .= td(
			$keys[$i]
		);
	}
	$td1.= td("Total");
	$tr = "";
	for($a=0;$a < $na;$a++){
		$totsum = 0;
		$name = sql_singleExec(sprintf("SELECT name FROM article WHERE artnr = %s",$artnrs[$a]) );
		$td2 = td($artnrs[$a] . " " . get_language_resource($name));
		for($i=0;$i<$nkeys;$i++){
			$tmpsum = $antall[$keys[$i]][$artnrs[$a]];
			if(!$tmpsum){
				$tmpsum = 0;
			}
			$totsum+=$tmpsum;
			$td2.=td($tmpsum);
		}
		$td2.=td($totsum);
		$tr .= tr($td2);
	}
	$td2.= td($totsum);
	$table = table(
		tr(
			td(
				big(bold("Data for $fradato til $tildato"))
				,
				array("colspan" => ($nkeys + 1))
			)
		).
		tr(
			$td1
		).
		$tr
		,
		array("border" => "1", "cellpadding" => "3", "cellspacing" => "0")
	);
	$output = $table;
	$table = table(
		tr(
			td(
				"Artikkelnr:"
			).
			td(
				input(
					"artnr"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Fra dato (yyyy-mm-dd)"
			).
			td(
				input(
					"fradato"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Til dato (yyyy-mm-dd)"
			).
			td(
				input(
					"tildato"
					,
					array("type" => "text")
				)
			)
		).
		tr(
			td(
				"Submit"
			).
			td(
				input(
					"mode"
					,
					array("type" => "hidden", "value" => "2")
				).
				input(
					"sumbit"
					,
					array("type" => "submit", "value" => "Kjør statistikk")
				)
			)
		)
		,
		array("border" => "1", "cellpadding" => "3", "cellspacing" => "0")
	);

	$output .= br().form(
		$table
		,
		array("method" => "post", "action" => "log_artikkelnr.php")
	).p().examples();

	echo eurofoto(array("body" => $output, "functions" => ""));
}

function examples(){
	//$ret = "Papirbilde: 1;418;419;2;3;4;5".br();
	$ret = "Mediaclip Gaver: 3001;3002;3003;3004;3005; 3006;3007;3008; 3009; 3010; 3011; 3011; 3012; 3013;3014;3015; 3016;3017;3018; 3019;3020;3021; 3022; 3023; 3024; 3025; 3026; 3027".br();
	$ret .= "Mediaclip Kalendere: 8000;8001;8002;8003;8004;8005;8006;8007;8008;8009;8010;910;911;912;913".br();
	$ret .= "Stabburet: 7196;7303;7302;7301;7304;7305;7746;7745".br();
	//$ret .= "Kort: 7;132;129;130;131;133".br();
	return $ret;
}

?>
