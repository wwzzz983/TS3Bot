<?php

	class Funkcje {

		private $clientlist = null;

		private $serverinfo = null;

		private $config = null;

		private static  $welcome_messege_list = [];

		private static  $older_admin_list = NULL;

		private static  $admins_ts_online_time_edition = NULL;

		private static  $aktualna_data = NULL;

		private static  $aktualnie_online = NULL;

		private static  $online_anty_vpn = [];

		private static  $ChannelNumberTime = NULL;

		private static  $czas_administracja_poke = [];

		private static  $czas_informacji_poke = [];

		private static  $servername_online = 0;

		private static  $update_activity_time = 0;

		private static  $tsAdmin = NULL;

		private static  $l = NULL;

		private static  $db = NULL;



		public function setClientlist($clientlist): void
		{
			$this->clientlist = $clientlist;
		}

		public function setServerinfo($serverinfo): void
		{
			$this->serverinfo = $serverinfo;
		}

		public function setConfig($config): void
		{
			$this->config = $config;
		}
		
		public function setTs3admin($ts3admin): void
		{
			self::$tsAdmin = $ts3admin;
		}
		
		public function setLang($lang): void
		{
			self::$l = $lang;
		}
		
		public function setDb($database): void
		{
			self::$db = $database;
		}

		public function admins_ts_online(): void
		{
			$admin_list_online  = NULL;
			$ranga = self::$l->heading_admins_ts_online;
			foreach($this->config['functions_admins_ts_online']['gid'] as $klucz => $value) {
				$servergroupclientlist =  self::$tsAdmin->getElement('data', self::$tsAdmin->serverGroupClientList($klucz, "-names"));
				$servergroupclientlistarray_filter = array_filter($servergroupclientlist[0]);
				if(!empty($servergroupclientlistarray_filter)){
					$ranga .= self::$l->sprintf(self::$l->group_admins_ts_online, $value);
					foreach($servergroupclientlist as $sgcl){
						foreach($this->clientlist as $cl){
						if($sgcl['cldbid'] == $cl['client_database_id']){
								$channelinfo = self::$tsAdmin->getElement('data', self::$tsAdmin->channelInfo($cl['cid']));
								$online = true;
								$channel = self::$l->sprintf(self::$l->channel_admins_ts_online, $cl['cid'], $channelinfo['channel_name']);
								$nick = self::$l->sprintf(self::$l->nick_admins_ts_online, $cl['client_database_id'], $cl['client_unique_identifier'], $cl['client_nickname']);
								break;
							}else{
								$online = false;
							}
						}
						if($online == true){
							$ranga .= self::$l->sprintf(self::$l->admins_ts_online, $nick, $channel);
							$admin_list_online .= $ranga;
						}else{
							$clientdbinfo = self::$tsAdmin->getElement('data', self::$tsAdmin->clientDbInfo($sgcl['cldbid']));
							$data = $this->przelicz_czas(time()-$clientdbinfo['client_lastconnected']);
							$txt_time = $this->wyswietl_czas($data, 1, 1, 1, 0, 0);
							$nick = self::$l->sprintf(self::$l->nick_admins_ts_online, $clientdbinfo['client_database_id'], $clientdbinfo['client_unique_identifier'], $clientdbinfo['client_nickname']);
							$ranga .= self::$l->sprintf(self::$l->admins_ts_offline, $nick, $txt_time);
						}
					}
					$ranga .= self::$l->size_admins_ts_online;
				}
			}
			if(self::$older_admin_list != $admin_list_online || self::$admins_ts_online_time_edition+60 < time()){
				self::$tsAdmin->channelEdit($this->config['functions_admins_ts_online']['cid'], array('channel_description' => $ranga));
				self::$older_admin_list = $admin_list_online;
				self::$admins_ts_online_time_edition = time()+60;
			}
		}

		public function aktualna_data(): void
		{
			$data = date($this->config['functions_aktualna_data']['format']);
			if($data != self::$aktualna_data){
				self::$tsAdmin->channelEdit($this->config['functions_aktualna_data']['cid'], array('channel_name' => self::$l->sprintf(self::$l->success_size_admins_ts_online, $data)));
				self::$aktualna_data = $data;
			}
		}

		public function aktualnie_online(): void
		{
			$count = $this->serverinfo['virtualserver_clientsonline'] - $this->serverinfo['virtualserver_queryclientsonline'];
			if(self::$aktualnie_online != $count){
				self::$tsAdmin->channelEdit($this->config['functions_aktualnie_online']['cid'], array('channel_name' => self::$l->sprintf(self::$l->success_aktualnie_online, $count, $this->padding_numbers($count, 'osoba', 'osoby', 'osób'))));
				self::$aktualnie_online = $count;
			}
		}

		public function anty_vpn(): void
		{
			$aktualnie_online = [];
			foreach($this->clientlist as $cl){
				if($cl['client_type'] == 0) {
					$aktualnie_online[$cl['clid']] = $cl['connection_client_ip'];
				}
			}
			$array_diff = array_diff($aktualnie_online, self::$online_anty_vpn);
			if(!empty($array_diff)){
				foreach($array_diff as $key => $value){
					if(!empty($value)){
						$jdc = json_decode(file_get_contents('http://legacy.iphub.info/api.php?showtype=4&ip='.$value));
						if($jdc->proxy == 1){
							$clientInfo = self::$tsAdmin->getElement('data', self::$tsAdmin->clientInfo($key));
							self::$tsAdmin->clientKick($key, 'server', self::$l->success_kick_anty_vpn);
							$this->log('Wyrzucono (client_nickname: '.$clientInfo['client_nickname'].') za używanie VPN.');
							unset($aktualnie_online[$key]);
						}
					}
				}
				self::$online_anty_vpn = $aktualnie_online;
			}
		}
		
		public function clean_channel(): void
		{
			$channellist = self::$tsAdmin->getElement('data', self::$tsAdmin->channelList("-topic -flags -voice -limits -icon"));
			$i = 0;
			foreach($channellist as $cl){
				if($cl['pid'] == $this->config['functions_clean_channel']['pid']){
					$i++;
					if($cl['channel_topic'] != 'WOLNY' && $cl['channel_topic'] != date('d.m.Y')){
						if(!empty(self::$tsAdmin->getElement('data', self::$tsAdmin->channelClientList($cl['cid'])))){
							self::$tsAdmin->channelEdit($cl['cid'], array('channel_topic' => date('d.m.Y')));
						}else{
							foreach($channellist as $cl2){
								if($cl2['pid'] == $cl['cid'] && !empty(self::$tsAdmin->getElement('data', self::$tsAdmin->channelClientList($cl2['cid'])))){
									self::$tsAdmin->channelEdit($cl['cid'], array('channel_topic' => date('d.m.Y')));
								}
							}
						}
						$czas_del = time()-604800;
						$czas = strtotime($cl['channel_topic']);
						if($czas <= $czas_del){
							self::$db->query("DELETE FROM `channel` WHERE `cid` = {$cl['cid']}");
							$data = array('channel_name' => $i.'. WOLNY', 'channel_topic' => 'WOLNY', 'channel_description' => '', 'channel_flag_maxfamilyclients_unlimited' => 0, 'channel_flag_maxclients_unlimited' => 0, 'channel_maxclients' => '0', 'channel_maxfamilyclients' => '0');
							self::$tsAdmin->channelEdit($cl['cid'], $data);
							foreach($channellist as $cl3){
								if($cl3['pid'] == $cl['cid']){
									self::$tsAdmin->channelDelete($cl3['cid']);
								}
							}
							$channelgroupclientlist = self::$tsAdmin->getElement('data', self::$tsAdmin->channelGroupClientList($cl['cid']));
							if(!empty($channelgroupclientlist)){
								foreach($channelgroupclientlist as $cgcl){
									self::$tsAdmin->setClientChannelGroup(8, $cl['cid'], $cgcl['cldbid']);
								}
							}
							$this->log('Usunięcie kanału za brak aktywności (channel name: '.$cl['channel_name'].')');
						}
					}
				}
			}
		}

		private function cenzor($txt, $add): bool
		{
			$cenzor = array('bit(h|ch)', '(ch|h)(w|.w)(d|.d)(p|.p)', '(|o)cip', '(|o)(ch|h)uj(|a)', '(|do|na|po|do|prze|przy|roz|u|w|wy|za|z|matkojeb)jeb(|a|c|i|n|y)', '(|do|na|naw|od|pod|po|prze|przy|roz|spie|roz|poroz|s|u|w|za|wy)pierd(a|o)', 'fu(ck|k)', '/[^.]+\.[^.]+$/', "/^(\"|').+?\\1$/", '(|po|s|w|za)(ku|q)rw(i|y)', 'k(у|u)rw', 'k(у|u)tas', '(|po|wy)rucha', 'motherfucker', 'piczk', '(|w)pi(z|z)d');
			if($add == 1){
				$cenzor = array_merge($this->config['functions_sprnick']['slowa'], $cenzor);
			}
			foreach($cenzor as $c) {
				if(preg_match('~'.$c.'~s', strtolower($txt))){
					return true;
				}
			}
			return false;
		}
		
		public function channelCreate(): void
		{
			$channelClientList = self::$tsAdmin->getElement('data', self::$tsAdmin->channelClientList($this->config['functions_channelCreate']['cid'], "-ip"));
			if(!empty($channelClientList)){
			foreach($channelClientList as $ccl){
				$spr_czy_ma_kanal = self::$db->query("SELECT COUNT(id) AS `count`, `cid` FROM `channel` WHERE `cldbid` = {$ccl['client_database_id']} OR `connection_client_ip` = '{$ccl['connection_client_ip']}'");
				while($scmk = $spr_czy_ma_kanal->fetch()){
						if($scmk['count'] != 0 && $scmk['cid'] != 0){
							$channelInfo = self::$tsAdmin->channelInfo($scmk['cid']);
							if(empty($channelInfo['errors'])){
								if($channelInfo['data']['channel_topic'] == 'WOLNY'){
									$count = 0;
									self::$db->query("DELETE FROM `channel` WHERE `cldbid` = {$ccl['client_database_id']}");
								}else{
									$count = 1;
								}
							}else{
								$count = 0;
								self::$db->query("DELETE FROM `channel` WHERE `cldbid` = {$ccl['client_database_id']}");
							}
						}else{
							$count = 0;
						}
					}
					if($count == 0){
						$zalozony = 0;
						$id = 1;
						$channellist = self::$tsAdmin->getElement('data', self::$tsAdmin->channelList('-topic'));
						foreach($channellist as $chl){
							if($chl['pid'] == $this->config['functions_channelCreate']['pid']){
								$id++;
								$editid = $id-1;
								if(trim($chl['channel_topic']) == 'WOLNY'){
									$data1 = [
										'channel_name' => $editid.'. '.$ccl['client_nickname'],
										'channel_topic' => date('d.m.Y'),
										'channel_description' => '',
										'channel_flag_maxfamilyclients_unlimited' => 1,
										'channel_flag_maxclients_unlimited' => 1,
										'channel_maxclients' => '-1',
										'channel_maxfamilyclients' => '-1'
									];
									self::$tsAdmin->channelEdit($chl['cid'], $data1);
									if($this->config['functions_channelCreate']['ile'] != 0){
										for($isub = 1; $isub <= $this->config['functions_channelCreate']['ile']; $isub++){
											$data = [ 'cpid' => $chl['cid'], 'channel_name' => $isub, 'channel_flag_permanent' => 1, 'channel_flag_maxclients_unlimited' => 1, 'channel_flag_maxfamilyclients_unlimited' => 1, 'channel_maxclients' => '-1', 'channel_maxfamilyclients' => '-1', 'channel_topic' => '' ];
											self::$tsAdmin->channelCreate($data);
										}
									}
									self::$tsAdmin->clientMove($ccl['clid'], $chl['cid']);
									self::$tsAdmin->setClientChannelGroup(5, $chl['cid'], $ccl['client_database_id']);
									self::$db->query("INSERT INTO `channel` VALUES (NULL, {$ccl['client_database_id']}, {$chl['cid']}, '{$ccl['connection_client_ip']}')");
									$this->log('Założono kanał dla (nick name: '.$ccl['client_nickname'].')');
									$zalozony = 1;
									break;
								}
							}
						}
						if($zalozony == 0){
							$data = [ 'cpid' => $this->config['functions_channelCreate']['pid'], 'channel_name' => $id.'. '.$ccl['client_nickname'], 'channel_flag_permanent' => 1, 'channel_flag_maxclients_unlimited' => 1, 'channel_flag_maxfamilyclients_unlimited' => 1, 'channel_maxclients' => '-1', 'channel_maxfamilyclients' => '-1', 'channel_topic' => date('d.m.Y') ];
							$channelCreate = self::$tsAdmin->channelCreate($data);
							if($this->config['functions_channelCreate']['ile'] != 0){
								for($isub = 1; $isub <= $this->config['functions_channelCreate']['ile']; $isub++){
									$data = array('cpid' => $channelCreate['cid'], 'channel_name' => $isub, 'channel_flag_permanent' => 1, 'channel_flag_maxclients_unlimited' => 1, 'channel_flag_maxfamilyclients_unlimited' => 1, 'channel_maxclients' => '-1', 'channel_maxfamilyclients' => '-1', 'channel_topic' => '');
									self::$tsAdmin->channelCreate($data);
								}
							}
							self::$tsAdmin->clientMove($ccl['clid'], $channelCreate['data']['cid']);
							self::$tsAdmin->setClientChannelGroup(5, $channelCreate['data']['cid'], $ccl['client_database_id']);
							self::$db->query("INSERT INTO `channel` VALUES (NULL, {$ccl['client_database_id']}, {$channelCreate['data']['cid']}, '{$ccl['connection_client_ip']}')");
							$this->log('Założono kanał dla nick name: '.$ccl['client_nickname']);
						}
					}else{
						self::$tsAdmin->clientMove($ccl['clid'], $this->config['functions_channelCreate']['cid_move']);
						self::$tsAdmin->clientPoke($ccl['clid'], self::$l->error_has_a_channel_channelCreate);
					}
				}
			}
		}

		public function ChannelNumber(): void
		{
			if(self::$ChannelNumberTime+10 < time()){
				$i = 0;
				$channellist = self::$tsAdmin->getElement('data', self::$tsAdmin->channelList('-topic'));
				foreach($channellist as $chl){
					if($chl['pid'] == $this->config['functions_ChannelNumber']['pid']){
						$i++;
						preg_match_all ("/(\d+)/is", $chl['channel_name'], $matches);
						if(empty($matches[1][0]) || $matches[1][0] != $i){
							self::$tsAdmin->channelEdit($chl['cid'], ['channel_name' => $i.$this->config['functions_ChannelNumber']['separator'].$chl['channel_name']]);
						}
					}
				}
				self::$ChannelNumberTime = time()+10;
			}
		}

		public function log($txt): void
		{
			if($this->config['functions_log']['on'] == true){
				$txt = '['.date('H:i:s').'] '.$txt."\n";
				$fp = @fopen('log/'.date('d.m.Y').'_log.log', "a"); 
				flock($fp, 2); 
				fwrite($fp, $txt); 
				flock($fp, 3); 
				fclose($fp);
			}
		}

		private function padding_numbers($number, $t1, $t2, $t3): string
		{
			$number %= 100;
			if($number == 0 || ($number >=5 && $number <=21)){
				return $t3;
			}
			if($number == 1){
				return $t1;
			}
			if($number > 1 && $number < 5){
				return $t2;
			}
			$number %= 10;
			if($number >1 && $number < 5){
				return $t2;
			}
			return $t3 ;
		}

		public function poke(): void
		{
			$administracja_po_poke = array();
			$admin_online = array();
			foreach(self::$czas_administracja_poke as $key => $value){
				if($value <= time()){
					unset(self::$czas_administracja_poke[$key]);
				}else{
					$administracja_po_poke[] = $key;
				}
			}
			foreach($this->config['functions_poke']['cid_gid'] as $channel => $value){
				if(empty(self::$czas_informacji_poke[$channel][0])){
					self::$czas_informacji_poke[$channel][1] = 0;
					self::$czas_informacji_poke[$channel][0] = 0;
				}
				$channelClientList = self::$tsAdmin->getElement('data', self::$tsAdmin->channelClientList($channel, '-groups -uid'));
				if(!empty($channelClientList)){
					foreach($channelClientList as $ccl){
						if(empty(array_intersect(explode(',', $ccl['client_servergroups']), $value))){
							$online_na_kanale[] = $ccl['clid'];
							$nicki[] =  self::$l->sprintf(self::$l->nick_poke, $ccl['cid'], $ccl['client_unique_identifier'], $ccl['client_nickname']);
						}else{
							$admin_online[] = $ccl['clid'];
						}
					}
					if(empty($admin_online)){
						$lista_adminow = array();
						foreach($this->clientlist as $cl) {
							if(!empty(array_intersect(explode(',',$cl['client_servergroups']), $value))){
								if(!in_array($cl['cid'], $this->config['functions_poke']['cidafk'])){
									$lista_adminow[] = $cl['clid'];
								}
							}
						}
						$administracja_poke = array_diff($lista_adminow, $administracja_po_poke);
						if(!empty($administracja_poke)){
							$nicki = implode(', ', $nicki);
							foreach($administracja_poke as $ap){
								self::$tsAdmin->clientPoke($ap, self::$l->sprintf(self::$l->success_admin_poke, $nicki));
								self::$czas_administracja_poke[$ap] = time()+$this->config['functions_poke']['admin_time'];
							}
							if(self::$czas_informacji_poke[$channel][0] == 0){
								foreach($online_na_kanale as $onk){
									self::$tsAdmin->sendMessage(1, $onk, self::$l->success_he_was_informed_poke);
								}
								self::$czas_informacji_poke[$channel][0] = 1;
								self::$czas_informacji_poke[$channel][1] = time()+$this->config['functions_poke']['user_time'];
							}	
						}else{
							if(self::$czas_informacji_poke[$channel][0] == 0){
								foreach($online_na_kanale as $onk){
									self::$tsAdmin->sendMessage(1, $onk, self::$l->error_admin_offline_poke);
								}
								self::$czas_informacji_poke[$channel][0] = 1;
								self::$czas_informacji_poke[$channel][1] = time()+$this->config['functions_poke']['user_time'];
							}
						}
					}else{
						foreach($admin_online as $ao){
							self::$czas_administracja_poke[$ao] = time()+$this->config['functions_poke']['admin_time'];
						}
						self::$czas_informacji_poke[$channel][0] = 1;
						self::$czas_informacji_poke[$channel][1] = time()+$this->config['functions_poke']['user_time'];
					}
				}
				if(self::$czas_informacji_poke[$channel][1] <= time()){
					self::$czas_informacji_poke[$channel][0] = 0;
				}
			}
		}

		private function przelicz_czas($time): array
		{
			$dni_r = $time / 86400;
			$data['d'] = floor($dni_r);
			$rzd = $time - $data['d'] * 86400;
			$godzin_r = $rzd / 3600;
			$data['H'] = floor($godzin_r);
			$rzg = $rzd - $data['H'] * 3600;
			$minut_r = $rzg / 60;
			$data['i'] = floor($minut_r);
			$data['s']  = $rzg - $data['i'] * 60;
			return $data;
		}
		
		public function register(): void
		{
			foreach($this->clientlist as $cl) {
				if($cl['client_type'] == 0) {
					$rangiexplode = explode(',', $cl['client_servergroups']);
					if(!in_array($this->config['functions_register']['gidm'], $rangiexplode) && !in_array($this->config['functions_register']['gidk'], $rangiexplode)){
						if($cl['cid'] == $this->config['functions_register']['cidm']){
								self::$tsAdmin->serverGroupAddClient($this->config['functions_register']['gidm'], $cl['client_database_id']);
								$this->log('Zarejestrowano nick name: '.$cl['client_nickname']);
						}
						if($cl['cid'] == $this->config['functions_register']['cidk']){
								self::$tsAdmin->serverGroupAddClient($this->config['functions_register']['gidk'], $cl['client_database_id']);
								$this->log('Zarejestrowano nick name: '.$cl['client_nickname']);
						}
					}
				}
			}
		}

		public function rekord_online(): void
		{
			$rekord = file_get_contents('includes/rekord.php');
			$count = $this->serverinfo['virtualserver_clientsonline'] - $this->serverinfo['virtualserver_queryclientsonline'];
			if($rekord < $count){
				self::$tsAdmin->channelEdit($this->config['functions_rekord_online']['cid'], array('channel_name' => self::$l->sprintf(self::$l->success_rekord_online, $count, $this->padding_numbers($count, 'osoba', 'osoby', 'osób'))));
				file_put_contents('includes/rekord.php', $count);
				$this->log('Ustanowiono rekord osób online: '.$count);
			}
		}
		
		public function servername(): void
		{
			$count = $this->serverinfo['virtualserver_clientsonline'] - $this->serverinfo['virtualserver_queryclientsonline'];
			if(self::$servername_online != $count){
				self::$tsAdmin->serverEdit(array('virtualserver_name' => str_replace('{1}', $count, $this->config['functions_servername']['name'])));
				self::$servername_online = $count;
			}
		}

		public function sprchannel(): void
		{
			$channellist = self::$tsAdmin->getElement('data', self::$tsAdmin->channelList());
			$i = 0;
			foreach($channellist as $cl){
				$delete = 0;
				if($cl['pid'] == $this->config['functions_sprchannel']['pid']){
					$i++;
					if($this->cenzor($cl['channel_name'], 0) == true){
						$delete = 1;
					}else{
						foreach($channellist as $cl2){
							if($cl2['pid'] == $cl['cid'] && $this->cenzor($cl2['channel_name'], 0) == true){
								$delete = 1;
							}
						}
					}
					if($delete == 1){
						$data = [ 'channel_name' => $i.'. WOLNY', 'channel_topic' => 'WOLNY', 'channel_description' => '', 'channel_flag_maxfamilyclients_unlimited' => 0, 'channel_flag_maxclients_unlimited' => 0, 'channel_maxclients' => '0', 'channel_maxfamilyclients' => '0' ];
						self::$tsAdmin->channelEdit($cl['cid'], $data);
						$channelClientList = self::$tsAdmin->getElement('data', self::$tsAdmin->channelClientList($cl['cid']));
						if(!empty($channelClientList)){
							foreach($channelClientList as $ccl){
								self::$tsAdmin->clientKick($ccl['clid'], 'channel', self::$l->kick_sprchannel);//lang
							}
						}
						foreach($channellist as $cl3){
							if($cl3['pid'] == $cl['cid']){
								self::$tsAdmin->channelDelete($cl3['cid']);
							}
						}
						self::$db->query("DELETE FROM `channel` WHERE `cid` = {$cl['cid']}");
						$this->log('Usunięcie kanału za wulgarną nazwę (channel name: '.$cl['channel_name'].')');
					}
				}
			}
		}

		public function sprnick(): void
		{
			foreach($this->clientlist as $cl) {
				if(!array_intersect(explode(',', $cl['client_servergroups']), $this->config['functions_sprnick']['gid'])){
					if($this->cenzor($cl['client_nickname'], 0) == true){
						self::$tsAdmin->clientPoke($cl['clid'], self::$l->poke_sprnick);
						self::$tsAdmin->clientKick($cl['clid'], "server", self::$l->kick_sprnick);
						$this->log('Wyrzucono użytkownika za wulgarny nick (client unique identifier: '.$cl['client_unique_identifier'].')');
					}
				}
			}
		}
		
		public function update_activity(): void
		{
			if(self::$update_activity_time <= time()){
				foreach($this->clientlist as $cl){
					$pobierz_czas_przebywania = self::$db->query("SELECT COUNT(id) AS `count` FROM `czas_przebywania` WHERE `cldbid` = {$cl['client_database_id']} LIMIT 1");
					while($pcp = $pobierz_czas_przebywania->fetch()){
						$count = $pcp['count'];
					}
					if($count == 0){
					self::$db->query("INSERT INTO `czas_przebywania` VALUES (NULL, {$cl['client_database_id']}, {$cl['clid']}, '{$cl['client_nickname']}', '{$cl['client_unique_identifier']}', 0)");
					}else{
						if($cl['client_idle_time'] < 300000){
							self::$db->query("UPDATE `czas_przebywania` SET `time` = time+60, `client_nickname` = '{$cl['client_nickname']}', `clid` = {$cl['clid']} WHERE `cldbid` = {$cl['client_database_id']}");
						}
					}
				}
				$s = 0;
				$top = NULL;
				if(!empty($this->config['functions_update_activity']['cldbid'])){
					$cldbid = implode(",", $this->config['functions_update_activity']['cldbid']);
				}
				$pobierz_top = self::$db->query("SELECT * FROM `czas_przebywania` WHERE `cldbid` NOT IN({$cldbid}) ORDER BY `time` DESC LIMIT 10");
				while($pt = $pobierz_top->fetch()){
					$s++;
					$data = $this->przelicz_czas($pt['time'], 1);
					$data = $this->wyswietl_czas($data, 1, 1, 1, 0, 0);
					$nick = "[B][URL=client://{$pt['cldbid']}/{$pt['cui']}]{$pt['client_nickname']}[/URL][/B]";
					$top .= "[SIZE=10][COLOR=#ff0000][B]{$s}.)[/B][/COLOR] {$nick} {$data}\n[/SIZE]";
				}
				self::$tsAdmin->channelEdit($this->config['functions_update_activity']['cid'], array('channel_description' => $top));
				self::$update_activity_time = time()+60;
			}
		}

		public function welcome_messege(): void
		{
			$listOfUser = [];
			foreach($this->clientlist as $cl) {
				if($cl['client_type'] == 0) {
					$listOfUser[] = $cl['clid'];
				}
			}
			$nowi = array_diff($listOfUser, self::$welcome_messege_list);
			if($nowi){
				foreach($nowi as $n) {
					$wmtxt = $this->config['functions_welcome_messege']['txt'];
					$clientInfo = self::$tsAdmin->getElement('data', self::$tsAdmin->clientInfo($n));
					$grupy =  explode(',', $clientInfo['client_servergroups']);
					if(in_array($this->config['functions_welcome_messege']['gid'], $grupy)){
						$wmtxt = $this->config['functions_welcome_messege']['txt_new'];
					}
					$search = [
						'%CLIENT_IP%', '%CLIENT_UNIQUE_ID%', '%CLIENT_DATABASE_ID%', '%CLIENT_ID%', '%CLIENT_CREATED%', '%CLIENT_COUNTRY%', '%CLIENT_VERSION%', '%CLIENT_PLATFORM%', '%CLIENT_NICKNAME%', '%CLIENT_TOTALCONNECTIONS%', '%CLIENT_LASTCONNECTED%', '%CLIENTONLINE%', '%MAXCLIENT%', '%HOUR%', '%DATE%'			
					];
	
					$replace = [
						$clientInfo['connection_client_ip'], $clientInfo['client_unique_identifier'], $clientInfo['client_database_id'], $n, date("H:i d.m.Y", $clientInfo['client_created']), $clientInfo['client_country'], $clientInfo['client_version'], $clientInfo['client_platform'], $clientInfo['client_nickname'], $clientInfo['client_totalconnections'], date("H:i d.m.Y",$clientInfo['client_lastconnected']), $this->serverinfo['virtualserver_clientsonline'] - $this->serverinfo['virtualserver_queryclientsonline'], $this->serverinfo['virtualserver_maxclients'], date('H:i'), date('d.m.Y')
					];
	
					$wmtxt = str_replace($search, $replace, $wmtxt);
					self::$tsAdmin->sendMessage(1, $n, $wmtxt);
				}
				self::$welcome_messege_list = $listOfUser;
			}
		}
		
		private function wyswietl_czas($data, $d=0, $h=0, $i=0, $s=0, $t=0):string
		{
			$txt_time = null;
			if($d==1){
				if($data['d'] == 0){
					$txt_time .= '';
				}else{
					self::$l->time_d1_wyswietl_czas = $this->padding_numbers($data['d'], self::$l->time_d1_wyswietl_czas, self::$l->time_d2_wyswietl_czas, self::$l->time_d2_wyswietl_czas);
					$txt_time .= $data['d'].' '.self::$l->time_d1_wyswietl_czas.' ';
				}
			}else{
				$data['d'] = 0;
			}
			if($h==1){
				if($data['d'] == 0 && $data['H'] == 0){
					$txt_time .= '';
				}else{
					self::$l->time_h1_wyswietl_czas = $this->padding_numbers($data['H'], self::$l->time_h1_wyswietl_czas, self::$l->time_h2_wyswietl_czas, self::$l->time_h3_wyswietl_czas);
					$txt_time .= $data['H'].' '.self::$l->time_h1_wyswietl_czas.' ';
				}
			}
			else{
				$data['H'] = 0;
			}
			if($i==1){
				if($data['d'] == 0 && $data['H'] == 0 && $data['i'] == 0){
					$txt_time .= '';
				}else{
					self::$l->time_i1_wyswietl_czas = $this->padding_numbers($data['i'], self::$l->time_i1_wyswietl_czas, self::$l->time_i2_wyswietl_czas, self::$l->time_i3_wyswietl_czas);
					$txt_time .= $data['i'].' '.self::$l->time_i1_wyswietl_czas.' ';
				}
			}else{
				$data['i'] = 0;
			}
			if($s==1){
				if($data['d'] == 0 && $data['s'] == 0 && $data['i'] == 0 && $data['H'] == 0){
					if($t == 0){
						$txt_time = '0 '.self::$l->time_s2_wyswietl_czas;
					}else{
						$txt_time = 'TERAZ';
					}
				}else{
					$l->time_s1_wyswietl_czas = $this->padding_numbers($data['s'], self::$l->time_s1_wyswietl_czas, self::$l->time_s2_wyswietl_czas, self::$l->time_s3_wyswietl_czas);
					$txt_time .= $data['s'].' '.self::$l->time_s1_wyswietl_czas;
				}
			}
			return $txt_time;
		}
	}
?>
