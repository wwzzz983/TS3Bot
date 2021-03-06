<?php

	return [

		'server' => [

			'login'		=> 'serveradmin', 									//ServerQuery Login
			'password'	=> '',												//ServerQuery password
			'ip'		=> '127.0.0.1',  									//IP serwera
			'port'		=> 9987, 											 //Server port
			'queryport'	=> 10011, 								 			//Query port
			'nick'		=> 'Bot'  											//Nick bota na ts

		],

		//addRank() Funkcja dodaje range po wejściu na kanało o podanym ID.
		'functions_addRank' => [

			'on'	=> false,													//true - włączona false - wyłączona
			'cid_gid'	=> [
			
							1 => 2
							
							]													//ID kanału, na który trzeba wejść wraz z ID rangi, którą ma nadać po wejściu. Tutaj 1 oraz 3 to ID kanału 2 oraz 4 ID rangi.

		],

	//admins_ts_online() Funkcja wyświetla listę administracji na kanale o podanym ID.
		'functions_admins_ts_online' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'cid'	=> 1,										//ID kanału, na którym ma być zmieniany opis.
			'gid'	=> [

				2	=> '[B][COLOR=#A12364]Admin[/COLOR][/B]',
				3	=> '[B][COLOR=#460070]Mod[/COLOR][/B]',
				4	=> '[B][COLOR=#7C08E8]KidMod[/COLOR][/B]'

			]													//ID Grupy wraz z nazwą rangi.

		],

	//aktualna_data() Funkcja ustawia aktualną datę jako nazwa kanału o podanym ID.
		'functions_aktualna_data' => [

			'on'		=> false,								//true - włączona false - wyłączona
			'cid'		=> 1,									//ID kanału, na którym ma ustawiać datę.
			'format'	=> 'd.m.Y H:i'							//Format daty d - dzień m - miesiąc Y - rok H - godzina i - minuta s - sekunda

		],

	//aktualnie_online() Funkcja ustawia aktualną liczbę osób online jako nazwa kanału o podanym ID.
		'functions_aktualnie_online' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'cid'	=> 1										//ID kanału, na którym ma ustawiać aktualną liczbę online.

		],

	//anty_vpn() Funkcja wyrzuca użytkowników, którzy posiadają proxy.
		'functions_anty_vpn' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'client_unique_identifier'	=> [

				''

			],													//Unique identifier użytkownika, którego ma nie wyrzucać za VPN.
			'key'						=> 'NTU4OnEzSmJZaENmWU1LcHJBYWw1VFN4enpVSGcwdkRFeHFs' //Klucz do API można go uzyskać na stronie https://iphub.info/pricing

		],

	//clean_channel() Funkcja czyści kanały, które nie są aktywne dłużej niż 7 dni w podanym sektorze.
		'functions_clean_channel' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'pid'	=> 1										//Strefa, w której ma sprawdzać kanały, które są nieaktywne.

		],

	//channelCreate() Funkcja zakłada kanały w podanym sektorze.
		'functions_channelCreate' => [

			'on'		=> false,								//true - włączona false - wyłączona
			'cid'		=> 1,									//ID kanału, na którego trzeba wejść, aby dostać kanał prywatny.
			'pid'		=> 2,									//Strefa, w której ma zakładać kanały prywatne.
			'ile'		=> 3,									//Liczba podkanałów.
			'channel_description'	=> '------------\n\nWłaściciel: %CLIENT_NICKNAME%\n\nData utworzenia: %DATE%\n\n------------', //Opis kanału %CLIENT_NICKNAME% - Nick właściciela kanału %DATE% - Data założenia %HOUR% - Godzina założenia
			'cid_move'	=> 4									//ID kanału, na który ma przenieść jeżeli użytkownik posiada już swój kanał.

		],
		
	//channelCreate() Funkcja sprawdza i w razie, czego poprawia numer kanału.
		'functions_ChannelNumber' => [

			'on'		=> false,								//true - włączona false - wyłączona
			'pid'		=> 1,									//Strefa, w której ma sprawdzać numery.
			'separator'		=> '. '								//Separator oddzielający nazwę kanału od numeru.

		],

	//log() Funkcja zapisuje logi z bota do pliku.
		'functions_log' => [

			'on'	=> false										//true - włączona false - wyłączona

		],

	//poke() Funkcja puka podane grupy jeżeli ktoś wbije na podany kanał.
		'functions_poke' => [

			'on'			=> false,							//true - włączona false - wyłączona
			'cid_gid'		=> [

				1 => [

					2, 3, 4

				],

				5 => [

					6, 7, 8

				]

			],													//ID Kanału, na który trzeba wejść, aby zaczepiło podane grupy.
			'cidafk' 		=> [

				9, 10

			],													//Kanały, na których administrator może być AFK.
			'poke_message' => 1,								//Wybór czy bot ma pukać czy wysyłać prywatną wiadomość do administratora 1 - Poke 2 - Wiadomość PW
			'admin_time'	=> 120,								//Czas, po którym ma pukać administratora ponownie.
			'user_time' 	=> 120								//Czas, po którym ma ponownie informować użytkownika.

		],

	//register() Funkcja automatycznie rejestruje użytkownika gdy on wbije na podane id kanału.
		 'functions_register' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'gidm'	=> 1,										//ID grupy zarejestrowanego.
			'cidm'	=> 2,										//ID kanału zarejestrowanego.
			'gidk'	=> 3,										//ID grupy zarejestrowanej.
			'cidk'	=> 4										//ID kanału zarejestrowanej.

		],
		
	//rekord_online() Funkcja ustawia rekord osób online jako nazwa kanału o podanym ID.
		'functions_rekord_online' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'cid'	=> 1										//ID kanału, na którym ma ustawiać rekord osób online.

		],
	//sendAd() Funkcja wysyła losową wiadomość na serwerze co określony czas.
		'functions_sendAd' => [

			'on'			=> false,							//true - włączona false - wyłączona.
			'time'			=> 10,								//Czas w minutach po jakim ma wysyłać losową wiadomość.
			'txt_group'		=> [
			
				['Bot stworzony przez Majcona ' =>	[ -1 ]],
				['Zaprawszam na pvp.iq.pl' =>	[ 0 ]],
				['Hop sialalalalalajka sprawie że Twoje życie to bedzie bajka' =>	[ 1, 2, 3 ]]

			]													//Treść oraz gdzie i do jakich grup ma wysyłać wiadomość -1 - Wiadomość jest wysyłana na czacie serwera 0 - Wiadomość jest wysyłana do wszytkich na PW 

		],

	//servername() Funkcja ustawia nazwę serwera wraz z liczbą osób online.
		'functions_servername' => [

			'on'	=> false,												//true - włączona false - wyłączona
			'name'	=> 'TS.PVP.IQ.PL ({1}/512)'								//Nazwa serwera, {1} zostanie zmienione na liczbę osób online.

		],

	//sprchannel() Funkcja sprawdza nazwy kanału pod względem wulgaryzmów.
		'functions_sprchannel' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'pid'	=> 78,										//Strefa, w której ma sprawdzać kanały, które zawierają wulgaryzmy w nazwie.

		],

	//sprnick() Funkcja sprawdza nicki użytkowników pod względem wulgaryzmów.
		'functions_sprnick' => [

			'on'	=> false,									//true - włączona false - wyłączona
			'slowa'	=> [

				'admin', 'root'

			],											//Dodatkowe słowa do cenzora słów.
			'gid'	=> [

				24, 146, 55

			]											//ID grup, które ma nie wyrzucać.

		],

		//statusTwitch() Funkcja ustawia w opisie status na kanale twitch.
		'functions_statusTwitch' => [

			'on'		=> false,						//true - włączona false - wyłączona
			'cid_name'	=> [

				1 => 'Majcon',
				2 => 'izakooo'

			]											//ID kanału oraz nick na twitch.tv
			
		],

		//top_connections() Funkcja ustawia w opisie kanału o podanym ID TOP 10 połączeń z serwerem.
		'functions_top_connections' => [

			'on'		=> false,												//true - włączona false - wyłączona
			'cid'		=> 1,													//ID kanału, w którym ma ustawiać TOP 10 połączeń z serwerem.

		],

		//Funkcja ustawia w opisie kanału o podanym ID TOP 10 Najdłuższych połączeń z serwerem.
		'functions_top_connections_time' => [

			'on'		=> false,
			'cid'		=> 1,													//ID kanału, w którym ma ustawiać TOP 10 połączeń z serwerem.
			'cldbid'	=> [

				1, 2, 3, 4, 5

			]																	//Client database id użytkowników, których ma nie wyświetlać w topce np. MusicBOT czy też ten bot.

		],

	//update_activity() Funkcja ustawia w opisie kanału o podanym ID TOP 10 aktywnych użytkowników.
		'functions_update_activity' => [

			'on'		=> false,												//true - włączona false - wyłączona
			'cid'		=> 1,													//ID kanału, w którym ma ustawiać TOP 10 online.
			'cldbid'	=> [

				1, 2, 3, 4, 5

			]																	//Client database id użytkowników, których ma nie wyświetlać w topce np. MusicBOT czy też ten bot.

		],

	//welcome_messege() Funkcja wysyła wiadomość powitalną.
		'functions_welcome_messege' => [

			'on'		=> false,															//true - włączona false - wyłączona
			'txt'		=> file_get_contents(__DIR__ . '/welcome_messege_txt.php'), 		//Tekst wiadomości, którą dostanie użytkownik po wejściu na serwer.
			'gid'		=> 8,																//ID grupy niezarejestrowanej, dla których ma wysyłać inną wiadomość.
			'txt_new'	=> file_get_contents(__DIR__ . '/welcome_messege_txt_new.php')		//Tekst wiadomości, którą dostanie nowy użytkownik po wejściu na serwer.

		]

	];
?>
