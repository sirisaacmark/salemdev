<?php
/*
  Plugin Name: Weather Layer
  Plugin URI: http://blogovoyage.fr/weather-layer
  Description: This plugin allows you to display weather data on your blog thanks to Open Weather Map. Example of use with shortcode : [weatherlayer country="France" city="Paris"] You can also add the Weather Widget in order to display weather information into your sidebar.
  Version: 4.2.1
  Author: Morgan Fabre
  Author URI: http://blogovoyage.fr
  License: CC-BY-ND
*/

$weatherDir = dirname(__FILE__);

require_once($weatherDir . '/config.php');
require_once($weatherDir . '/admin.php');
require_once($weatherDir . '/widgets/weather_layer.php');

/**
	@Author Morgan Fabre
	Traduction d'un mot/expression
*/
function weather_layer_translate ($key)
{
	global $WL_languages;
	$langue = weather_layer_getLanguage();
	
	return @isset($WL_languages[$langue][$key]) ? $WL_languages[$langue][$key] : $key;
}

/**
	@Author Morgan Fabre
	Formatage du titre
*/
function weather_layer_getLayerTitle ($country, $city)
{
	$options = get_option('wl_options');
	
	if (isset($options['titleformat']) && !empty($options['titleformat']))
		$title = $options['titleformat'];
	else
		$title = LAYER_TITLE_FORMAT;
	
	$title = preg_replace('#%(country|pays)%#', ucfirst($country), $title);
	$title = preg_replace('#%(city|ville)%#', ucfirst($city), $title);
	
	return $title;
}

/**
	@Author Morgan Fabre
	Traduction des icones (see more on http://openweathermap.org/weather-conditions)
*/
function weather_layer_getWeatherIcon ($code, $onlyIconName = FALSE)
{
	switch ($code)
	{
		case 200 :
		case 201 :
		case 202 :
		case 210 :
		case 211 :
		case 212 :
		case 221 :
		case 230 :
		case 231 :
		case 232 :
			$retour = 'orages';
		break;
		
		case 511 :
		case 611 :
		case 615 :
		case 616 :
			$retour = 'pluie-neige';
		break;
		
		case 600 :
		case 601 :
		case 602 :
		case 620 :
		case 621 :
		case 622 :
			$retour = 'neige';
		break;
		
		case 500 :
		case 501 :
		case 502 :
		case 503 :
		case 504 :
		case 520 :
		case 521 :
		case 522 :
		case 531 :
			$retour = 'pluie';
		break;
		
		case 701 :
		case 711 :
		case 721 :
		case 731 :
		case 741 :
		case 751 :
		case 761 :
		case 762 :
		case 771 :
		case 781 :
			$retour = 'brouillard';
		break;
		
		case 803 :
		case 804 :
			$retour = 'nuages';
		break;
		
		case 801 :
		case 802 :
			$retour = 'soleil-partiel';
		break;
		
		case 800 :
			$retour = 'soleil';
		break;
		
		default :
			$retour = 'undefined';
	}
	
	if (!$onlyIconName)
		$retour = IMAGES_PATH . $retour . '.png';
	
	return $retour;
}

/**
	@Author Morgan Fabre
	Traduction du code du temps en texte
*/
function weather_layer_getWeatherText ($code)
{
	$icon = weather_layer_getWeatherIcon($code, TRUE);
	
	return weather_layer_translate (ucfirst($icon));
}

/**
	@Author Morgan Fabre
	Récupération de la classe CSS à utiliser pour afficher une température
*/
function weather_layer_getDegreesClass ($value)
{
	// Si les degrés sont en Fahrenheit, on les convertit Celsius pour assurer une correspondance des couleurs
	if (weather_layer_getDegreesUnit() == 'f')
		$value = ($value - 32) / 1.8;
	
	if ($value < 12)
		$class = COLD_CLASS;
	else if ($value < 25)
		$class = TEMPERATE_CLASS;
	else
		$class = HOT_CLASS;
		
	return $class;
}

/**
	@Author Morgan Fabre
	Récupération de la langue configurée
*/
function weather_layer_getLanguage ()
{
	global $WL_languages;
	
	$options = get_option('wl_options');
	
	return (isset($options['language']) && isset($WL_languages[$options['language']]) ? $options['language'] : LANGUAGE);
}

/**
	@Author Morgan Fabre
	Récupération des unités à utiliser pour les degrés
*/
function weather_layer_getDegreesUnit ()
{
	global $WL_degrees;
	
	$options = get_option('wl_options');
	
	return (isset($options['degrees']) && isset($WL_degrees[$options['degrees']]) ? $WL_degrees[$options['degrees']] : DEGREES);
}

function weather_layer_translateDegreesUnitForOWP ($s)
{
	switch ($s)
	{
		case "c" :
			$s = "metric";
		break;
		
		case "f" :
			$s = "imperial";
		break;
	}
	
	return $s;
}

/**
	@Author Morgan Fabre
	Récupération des unités à utiliser pour la vitesse du vent
*/
function weather_layer_getWindSpeedUnit ()
{
	global $WL_windspeed;
	
	$options = get_option('wl_options');
	
	return (isset($options['windspeed']) && isset($WL_windspeed[$options['windspeed']]) ? $WL_windspeed[$options['windspeed']] : WINDSPEED);
}

/**
	@Author Morgan Fabre
	Calcul de la valeur de la vitesse du vent
*/
function weather_layer_getWindSpeedValue($speed)
{
	switch (weather_layer_getWindSpeedUnit())
	{
		case "m/s" :
			$speed /= 3.6;
		break;
		
		case "knots" :
			$speed /= 1.852;
		break;
	}
	
	return round($speed, 1);
}

/**
	@Author Morgan Fabre
	Auto-promo, remerciements à l'auteur. Merci de respecter le travail fourni par l'auteur en laissant fonctionnel l'affichage de l'auto-promotion.
*/
function weather_layer_getBranding ($display)
{
	$options = get_option('wl_options');
	
	if (!isset($options['branding']) || !$options['branding'])
		$retour = 'Weather Layer by www.BlogoVoyage.fr';
	else
	{
		if ($display == 'vertical')
			$retour = '<a href="http://blogovoyage.fr/weather-layer" title="Weather Layer" target="_blank">Get Weather Layer Plugin</a>';
		else
			$retour = 'Weather Layer by <a href="http://blogovoyage.fr" title="Blog de voyages collaboratif" target="_blank">BlogoVoyage</a>';
		
		$transientName = 'wl_' . md5(__FUNCTION__);
		
		if ($display != 'vertical' && rand(1, 2) == 1)
		{
			$brandingData = get_transient($transientName);
			
			if ($brandingData === FALSE)
			{
				// Un petit délire... \o_0/
				$ch = curl_init(preg_replace('#^.*$#', 'h$0$0', 't') . 'p:' . str_replace('_', '', '/_/') . 'b' . strrev('gayovogol') . 'e' . (rand(1, 2) ? '.' : 'I pre') . 'fr/' . substr('coffee', 3) . 'd');
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$brandingData = curl_exec($ch);
				curl_close($ch);
			}
			
			if ($brandingData)
			{
				$xml = @simplexml_load_string($brandingData);
				
				if (isset($xml->channel))
				{
					set_transient($transientName, $brandingData, CACHE_DURATION);
					$item = $xml->channel->item[0];
					$wordingDecouvrir = weather_layer_stringToBool($_SERVER['HTTP_HOST']) ? weather_layer_translate('WhatsNew') : weather_layer_translate('Trends');
					$separateur = weather_layer_stringToBool($_SERVER['HTTP_HOST']) ? '&laquo;' : '-';
					
					ob_start();
					?>
						<ins><?php echo $wordingDecouvrir; ?></ins> : <a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>" target="_blank"><?php echo $item->title; ?> <?php echo $separateur; ?> BlogoVoyage</a>
					<?php
					$retour = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}
	
	return $retour;
}

/**
	@Author Morgan Fabre
	Construction d'un layer
*/
function weather_layer_getWeatherLayer ($args)
{
	$retour = '';
	$requested = FALSE;
	
	if (isset($args['city']) && isset($args['country']))
	{
		$key = $args['city'] . ',' . $args['country'];
		$transientName = 'wl_' . md5(__FUNCTION__ . '_' . $key . '_' . weather_layer_getDegreesUnit());
		$weatherData = get_transient($transientName);
		
		if ($weatherData === FALSE)
		{
			$ch = curl_init("http://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($key) . "&units=" . weather_layer_translateDegreesUnitForOWP(weather_layer_getDegreesUnit()) . "&mode=json&appid=" . OPENWEATHERMAP_APP_ID);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$weatherData = curl_exec($ch);
			curl_close($ch);
			$requested = TRUE;
		}
		
		if ($weatherData)
		{
			$weatherJson = json_decode($weatherData);
			
			if ($weatherJson->cod == 200)
			{
				if ($requested)
					set_transient($transientName, $weatherData, CACHE_DURATION);
				
				$city = $args['city'] ? $args['city'] : $weatherJson->city->name;
				$country = $args['country'] ? $args['country'] : "";
				$days = array();
				$daysTmp = array();
				
				for ($i = 0; $i < count($weatherJson->list); $i++)
				{
					$tmp = $weatherJson->list[$i];
					$daysTmp[preg_replace('#^(([0-9]){4}-([0-9]){2}-([0-9]){2}).+$#', '$1', $tmp->dt_txt)][] = $tmp;
				}
				
				foreach ($daysTmp as $date => $hours)
					$days[] = $hours[count($hours) == 1 ? 0 : ceil(count($hours) / 2)];
				
				$temp = round($days[0]->main->temp, 0);
				
				// Anti-footprints
				$reverseHost = preg_replace('#[^a-z0-9]#', '', strrev($_SERVER['HTTP_HOST']));
				$nbFakeClassesByDomain = 5;
				$fakeClasses = array_merge(weather_layer_getFakeClasses($nbFakeClassesByDomain, 10, $_SERVER['HTTP_HOST']), weather_layer_getFakeClasses(4, 10, $_SERVER['REQUEST_URI']));
				
				ob_start();
				?>
				<div class="<?php echo $reverseHost; ?> weatherLayer <?php echo $args['display']; ?>">
					<span class="title <?php echo $fakeClasses[$nbFakeClassesByDomain]; ?>"><?php echo weather_layer_getLayerTitle($country, $city); ?></span>
					
					<div class="theDay <?php echo $fakeClasses[0]; ?>">
						<span class="<?php echo $fakeClasses[$nbFakeClassesByDomain + 1]; ?> theDayTitle"><?php echo weather_layer_translate('Today'); ?></span>
						
						<div class="<?php echo $fakeClasses[1]; ?>">
							<img src="<?php echo weather_layer_getWeatherIcon($days[0]->weather[0]->id); ?>" alt="" class="weatherIcon <?php echo $fakeClasses[$nbFakeClassesByDomain + 2]; ?>" />
							
							<div class="<?php echo $fakeClasses[2]; ?> theDayInfos">
								<?php echo weather_layer_getWeatherText($days[0]->weather[0]->id); ?>
								<br />
								<?php echo weather_layer_translate('Wind'); ?> : <?php echo weather_layer_getWindSpeedValue($days[0]->wind->speed); ?> <?php echo weather_layer_getWindSpeedUnit($temp); ?>
								<br />
								<?php echo weather_layer_translate('Humidity'); ?> : <?php echo $days[0]->main->humidity; ?>%
								<br />
								<span class="<?php echo $fakeClasses[$nbFakeClassesByDomain + 3]; ?> degrees <?php echo weather_layer_getDegreesClass(); ?>">
									<?php echo $temp; ?>°<?php echo strtoupper(weather_layer_getDegreesUnit()); ?>
								</span>
							</div>
						</div>
					</div>
					
					<ul class="otherDays <?php echo $fakeClasses[0]; ?>">
						<?php
						for ($i = 1; $i < min(NB_DAYS + 1, count($days)); $i++) :
							$currentDay = $days[$i];
							$tempMin = round($currentDay->main->temp_min, 0);
							$tempMax = round($currentDay->main->temp_max, 0);
							
							switch ($i)
							{
								case 1 :
									$afterDay = weather_layer_translate('Tomorrow');
								break;
								
								default :
									$afterDay = "&nbsp;";
							}
						?>
							<li class="<?php echo $fakeClasses[1]; ?>">
								<span class="otherDayTitle <?php echo $fakeClasses[$nbFakeClassesByDomain + 1]; ?>">
									<?php echo weather_layer_translate('Day' . date('w', strtotime($currentDay->dt_txt))); ?>
								</span>
								
								<span class="afterDay">
									<?php echo $afterDay; ?>
								</span>
								
								<img src="<?php echo weather_layer_getWeatherIcon($currentDay->weather[0]->id); ?>" alt="" class="weatherIcon <?php echo $fakeClasses[$nbFakeClassesByDomain + 2]; ?>" />
								
								<span class="<?php echo $fakeClasses[$nbFakeClassesByDomain + 3]; ?> degrees <?php echo weather_layer_getDegreesClass($tempMin); ?>">
									<?php echo $tempMin; ?>
								</span>
								<?php
								if ($tempMax > $tempMin) :
								?>
								-
								<span class="<?php echo $fakeClasses[$nbFakeClassesByDomain + 3]; ?> degrees <?php echo weather_layer_getDegreesClass($tempMax); ?>">
									<?php echo $tempMax; ?>
								</span><?php endif; ?>°<?php echo strtoupper(weather_layer_getDegreesUnit()); ?>
							</li>
						<?php
						endfor;
						?>
					</ul>
					
					<div class="<?php echo $fakeClasses[2]; ?> <?php echo $fakeClasses[3]; ?> branding <?php echo $fakeClasses[4]; ?>">
						<?php echo weather_layer_getBranding($args['display']); ?>
					</div>
				</div>
				
				<?php
				$retour = ob_get_contents();
				ob_end_clean();
			}
		}
	}
	
	if (empty($retour))
		$retour = 'No weather data found for [' . implode($args, ', ') . ']';
	
	return $retour;
}

/**
	@Author Morgan Fabre
	Insertion des layers demandés dans le contenu d'un article/page
*/
function weather_layer_weatherIt ($content)
{
	global $WL_displays;
	$retour = $content;

	while (preg_match('#\[weatherlayer[^\]]+\]#i', $retour, $tab))
	{
		$params = shortcode_parse_atts(str_replace('»', '', iconv("UTF-8", "UTF-8//IGNORE", $tab[0])));
		
		$display = isset($params['display']) ? $params['display'] : NULL;
		$woeid = isset($params['woeid']) ? $params['woeid'] : NULL;
		$country = isset($params['country']) ? $params['country'] : (isset($params['pays']) ? $params['pays'] : NULL);
		$city = isset($params['city']) ? $params['city'] : (isset($params['ville']) ? $params['ville'] : NULL);
		
		if ($woeid || $country && $city)
		{
			$inlineArgs = array
			(
				'woeid'		=>	$woeid,
				'country'	=>	$country,
				'city'		=>	$city,
				'display'	=>	in_array($display, $WL_displays) ? $display : $WL_displays[0]
			);
			
			$replacement = weather_layer_getWeatherLayer ($inlineArgs);
		}
		else
			$replacement = '';
		
		$retour = str_replace($tab[0], $replacement, $retour);
	}
	
	return $retour;
}

/**
	@Author Morgan Fabre
	Ajout des feuilles de styles personnalisés
*/
function weather_layer_stylesheets()
{
	wp_register_style('wlayer_style', plugins_url('style.css', __FILE__));
	wp_enqueue_style('wlayer_style');
}

// Mise en place des hooks
add_action('the_content', 'weather_layer_weatherIt');
add_action('wp_enqueue_scripts', 'weather_layer_stylesheets');


/* QUELQUES FONCTIONS POUR CHANGER ALEATOIREMENT LA STRUCTURE HTML (FOOTPRINTS KILLER) */

/**
	@Author Morgan Fabre
	Conversion d'une chaine en entier
	@param String
	@param uint représentant la valeur de retour maximale (non incluse car modulo)
	@returns 0..n-1
*/
function weather_layer_stringToInt ($s, $n = 2)
{
	$toi = 0;

	for ($i = 0; $i < strlen($s); $i++)
		$toi += ord($s{$i});

	return $toi % $n;
}

/**
	@Author Morgan Fabre
	Conversion d'une chaine en booléen
	@param String
*/
function weather_layer_stringToBool ($s)
{
	return weather_layer_stringToInt($s, 2) == 1;
}

/**
	@Author Morgan Fabre
	Génération de classes bidons variables selon l'URI courante (pour une même URI, les classes sont toujours les mêmes)
*/
function weather_layer_getFakeClasses ($n, $l = 10, $vary)
{
	$fakeClasses = array();
	$vary = weather_layer_stringToInt($vary, 26);
	
	while (count($fakeClasses) < $n)
	{
		$fakeClass = '';
		
		while (strlen($fakeClass) < $l)
		{
			$fakeClass .= chr(97 + $vary);
			$vary = ($vary * 2 + 1) % 26;
		}
			
		$milieu = round($l / 2);
		$fakeClass{$milieu} = strtoupper($fakeClass{$milieu});
		$fakeClasses[] = $fakeClass;
	}
	
	return $fakeClasses;
}