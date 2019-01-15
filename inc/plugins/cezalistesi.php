<?php
$plugins->add_hook('misc_start', 'liste');

if(!defined("IN_MYBB")) {
    die("Yanlis yerdesin!");
}
require ('cezalistesi/functions.php');
function cezalistesi_info()
{
    return array(
        "name"          =>      "Ceza Listesi",
        "description"   =>      "Aciklama alani",
        "author"        =>      "Kerem",
        "version"       =>      1.0,
        "compatibility" =>      "*"
    );
}

function cezalistesi_install()
{
    $template = '<html>
	<head>
		<title>{$lang->cezalistesi} :: {$mybb->settings[\'bbname\']} </title>
		{$headerinclude}
	</head>
	<body>
		{$header}
		<div class="row vertical-align bg-brown rounded-top p-2 mx-0 shadow-sm">
			<div class="container bg-saydam rounded p-2 shadow-sm">
				<div class="col-12 text-left text-white px-0">
					<i class="fas fa-server ml-1 mr-2"></i>{$lang->cezalistesi}
				</div>
			</div>
		</div>
		<div class="bg-light rounded-bottom px-2 py-1 shadow-sm mb-2">
			<div class="container">
				<div class="row bg-white opacity rounded shadow-sm mb-2 p-2 text-muted fz-11">
					<div class="col-1 pr-0">
						{$lang->ulke}
					</div>
					<div class="col-2 pl-0">
						{$lang->cezatarihi}
					</div>
					<div class="col-2 px-0">
						{$lang->oyuncuadi}
					</div>
					<div class="col-3">
						{$lang->cezasebebi}
					</div>
					<div class="col-2">
						{$lang->cezaveren}
					</div>
					<div class="col-2">
						{$lang->sure}
					</div>
				</div>
			</div>
			<div class="accordion" id="accordionExample">
			{$liste_row}
			</div>
			<div class="container">
				<div class="row bg-white opacity rounded shadow-sm mb-2 p-2 text-muted fz-11">
					<div class="col-auto">
						{$lang->cl_istatistik}
					</div>
				</div>
			</div>
		</div>

		{$pagination}


		{$footer}
	</body>
</html>';
    $insert_array = array(
        'title' => 'cl_anasayfa',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
    
    $template = '<div id="diger" class="d-none">
	<textarea class="form-control form-control-sm p-2 mt-3" name="other">{$sonuc[\'reason\']}</textarea>
</div>';
    $insert_array = array(
        'title' => 'cl_bansebebi_other',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<option data-tokens="{$value}" value="{$value}" {$sec}>{$value}</option>';
    $insert_array = array(
        'title' => 'cl_bansebebi_row',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<html>
	<head>
		<title>{$lang->cezalistesi} :: {$mybb->settings[\'bbname\']} </title>
		{$headerinclude}
		<link rel="stylesheet" href="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.css">
		<script src="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.js"></script>


	</head>
	<body onload="diger();">
		{$header}{$dogru}
		
		<form action="{$mybb->settings[\'bburl\']}{$url}" method="post" onSubmit="window.location.reload()">
			<div class="row">
				{$cl_nav}
				<div class="col-12 col-md-10">

					

								{$bildiri}

					<div class="row vertical-align bg-acikturuncu rounded-top p-2 mx-0 shadow-sm">
						<div class="container bg-saydam rounded p-2 shadow-sm">
							<div class="col-12 text-left text-white px-0">
								{$lang->duzenle}
							</div>
						</div>
					</div>
					<div class="row mx-0 mb-3 p-2 bg-light shadow-sm rounded">
						<div class="col-12 px-0 mb-2 mb-sm-0">
							<div class="bg-white p-2 shadow-sm rounded text-muted overflow-hidden">
								<div class="row">
									<div class="col-12 py-md-3">
										<div class="row vertical-align mb-2 mb-md-3">
											<div class="col-12 col-md-3 px-md-4 pr-md-0 py-2">
												{$lang->oyuncu_adi}
											</div>
											<div class="col-12 col-md-7">
												<input type="text" name="name" id="name" value="{$name}" class="form-control form-control fz-12">
											</div>	
										</div>
										<div class="row vertical-align mb-2 mb-md-3">
											<div class="col-12 col-md-3 px-md-4 pr-md-0 py-2">
												{$lang->steamid}
											</div>
											<div class="col-12 col-md-7">
												<input type="text" name="authid" id="authid" value="{$authid}" class="form-control form-control fz-12">
											</div>	
										</div>
										<div class="row vertical-align mb-2 mb-md-3">
											<div class="col-12 col-md-3 px-md-4 pr-md-0 py-2">
												{$lang->ipadresi}
											</div>
											<div class="col-12 col-md-7">
												<input type="text" name="ip" id="ip" value="{$ip}" class="form-control form-control fz-12">
											</div>	
										</div>
										<div class="row vertical-align mb-2">
											<div class="col-12 col-md-3 px-md-4 pr-md-0 py-2">
												{$lang->cezasebebi}
											</div>
											<div class="col-12 col-md-7">
												<select class="selectpicker fz-12" data-live-search="true" name="reason" id="reason" onchange="diger()">
													{$bansebepleri_row}
												</select>
												{$other}
											</div>
										</div>
										<div class="row vertical-align mb-2">
											<div class="col-12 col-md-3 px-md-4 pr-md-0 py-2">
												{$lang->sure}
											</div>
											<div class="col-12 col-md-7">
												<select class="selectpicker fz-12" data-live-search="true" name="length" id="length" onchange="diger()">
													{$banssureleri_row}
												</select>
											</div>
										</div>


										<div class="row">
											<div class="col-12 mx-auto text-center my-2 mt-md-3 mb-md-0">
												<input type="submit" class="btn btn-sm btn-success px-3 py-2" name="submit" value="{$lang->kaydet}">
											</div>
										</div>

									</div>

								</div>


							</div>

						</div>							
					</div>					
				</div>

			</div>
			</div>


		</form>
	<script>
		function diger() {
			var x = document.getElementById("reason").value;
			console.log(x);
			if( x == "Diğer" )
				document.getElementById("diger").className = "d-block";
			else
				document.getElementById("diger").className = "d-none";
		}
	</script>

	{$footer}

	</body>
</html>';
    $insert_array = array(
        'title' => 'cl_edit',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<html>
	<head>
		<title>{$lang->cezalistesi} :: {$mybb->settings[\'bbname\']} </title>
		{$headerinclude}
		<link rel="stylesheet" href="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.css">
		<script src="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.js"></script>


	</head>
	<body onload="diger();">
		{$header}

		<form action="{$mybb->settings[\'bburl\']}{$url}" method="post" onSubmit="window.location.reload()">
			<div class="row">
				{$cl_nav}
				<div class="col-12 col-md-10">




					<div class="row vertical-align bg-acikturuncu rounded-top p-2 mx-0 shadow-sm">
						<div class="container bg-saydam rounded p-2 shadow-sm">
							<div class="col-12 text-left text-white px-0">
								{$lang->duzenle}
							</div>
						</div>
					</div>
					<div class="row mx-0 mb-3 p-2 bg-light shadow-sm rounded">
						<div class="col-12 px-0 mb-2 mb-sm-0">
							<div class="bg-white p-2 shadow-sm rounded text-muted overflow-hidden">
								{$bildiri}
								asd
							</div>							
						</div>					
					</div>

				</div>
			</div>


		</form>
		<script>

			{$footer}

			</body>
			</html>';
    $insert_array = array(
        'title' => 'cl_kaldir',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<div class="container bg-white opacity rounded shadow-sm mb-2 px-0">
	<div class="d-block" data-toggle="collapse" data-target="#bid{$sonuc[\'bid\']}" aria-expanded="true" aria-controls="bid{$sonuc[\'bid\']}">
		<div class="row vertical-align mb-1 py-2 text-muted cursor-pointer pl-4 pr-3" data-toggle="collapse" href="#bid{$sonuc[\'bid\']}" role="button" aria-expanded="false" aria-controls="bid{$sonuc[\'bid\']}">
			<div class="col-1 pr-0">
				<i class="fas fa-flag"></i>
			</div>
			<div class="col-2 pl-0">
				{$tarih}
			</div>
			<div class="col-2 px-0 text-dark">
				{$nick}
			</div>
			<div class="col-3">
				{$sebep}
			</div>
			<div class="col-2">
				{$yetkili_adi}
			</div>
			<div class="col-2">
				{$kalanzaman}
			</div>
		</div>
	</div>

	<div id="bid{$sonuc[\'bid\']}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
		<div class="border-top p-2">
			<div class="row vertical-align">
				<div class="col-4">
					<div class="row vertical-align">
						<div class="col-5 text-muted mb-2">SteamID</div>
						<div class="col-6 mb-2"><input class="form-control form-control-sm fz-11" disabled value="{$steamid32}"></div>
						<div class="col-5 text-muted">SteamID</div>
						<div class="col-6 mb-2"><input class="form-control form-control-sm fz-11" disabled value="{$steamid3}"></div>
						<div class="col-5 text-muted">Community ID</div>
						<div class="col-6 mb-2"><input class="form-control form-control-sm fz-11" disabled value="{$steamid64}"></div>
					</div>
				</div>
				<div class="col-4">
					
					<div class="row vertical-align fz-11">
						<div class="col-5 text-muted mb-2">{$lang->ipadresi}</div>
						<div class="col-6 mb-2">{$ip}</div>
						<div class="col-5 text-muted mb-2">{$lang->baslangic}</div>
						<div class="col-6 mb-2">{$baslangic}</div>
						<div class="col-5 text-muted mb-2">{$lang->bitis}</div>
						<div class="col-6 mb-2">{$bitis}</div>
						<div class="col-5 text-muted mb-2">{$lang->sure}</div>
						<div class="col-6 mb-2">{$sure}</div>
						<div class="col-5 text-muted mb-2">{$lang->cezayeri}</div>
						<div class="col-6 mb-2">{$cezayeri}</div>
					</div>
					
				</div>
				<div class="col-4">
					
					{$duzenle}
					{$kaldir}
					
				</div>
			</div>
		</div>
	</div>


</div>';
    $insert_array = array(
        'title' => 'cl_liste_row',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<div class="col-12 col-md-2 pr-md-0 mb-3">
	<div class="list-group rounded  shadow-sm">
		<div class="bg-brown rounded-top p-2 shadow-sm">
			<div class="bg-saydam text-white p-2 shadow-sm rounded">
				{$lang->cl_nav}
			</div>
		</div>
		<a class="list-group-item list-group-item-action disabled py-2 px-3 text-muted fz-11">Disabled menu</a>
		<a href="private.php?action=folders" class="list-group-item list-group-item-action py-2 px-3 fz-11">Normal menu</a>
		{$cl_nav_row}
	</div>
</div>';
    $insert_array = array(
        'title' => 'cl_nav',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<a href="{$mybb->settings[\'bburl\']}/{$key}" class="list-group-item list-group-item-action py-2 px-3 fz-11">{$value}</a>';
    $insert_array = array(
        'title' => 'cl_nav_row',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);

    $template = '<html>
	<head>
		<title>{$lang->cezalistesi} :: {$mybb->settings[\'bbname\']} </title>
		{$headerinclude}
		<link rel="stylesheet" href="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.css">
		<script src="https://tf2turkiye.com/jscripts/2019/bootstrap-select.min.js"></script>


	</head>
	<body onload="diger();">
		{$header}

		<form action="{$mybb->settings[\'bburl\']}{$url}" method="post" onSubmit="window.location.reload()">

			<div class="row vertical-align bg-danger rounded-top p-2 mx-0 shadow-sm">
				<div class="container bg-saydam rounded p-2 shadow-sm">
					<div class="col-12 text-left text-white px-0">
						{$lang->cezakaldir}
					</div>
				</div>
			</div>
			<div class="row mx-0 mb-3 p-2 bg-light shadow-sm rounded">
				<div class="col-12 px-0 mb-2 mb-sm-0">
					<div class="bg-white p-2 shadow-sm rounded text-muted overflow-hidden">
						{$bildiri}
						<div class="row">
							<div class="col-12">
								{$lang->cezayikaldir}
							</div>
						</div>

					</div>

				</div>							
			</div>	
			
			<input type="hidden" id="kaldir_dugme" name="kaldir_dugme" value="1">
			<div class="text-center">
				<input type="submit" class="btn btn-sm btn-danger px-3 py-2" id="submit" name="submit" value="{$lang->cezakaldir}">
			</div>


		</form>

		{$footer}

	</body>
</html>';
    $insert_array = array(
        'title' => 'cl_unban',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);


}
