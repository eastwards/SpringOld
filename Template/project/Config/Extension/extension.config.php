<?
//Memcache数据缓存
$configs[] = array(
'id'        => 'mem',
'enable'    => true,
'source'    => LibDir.'/Util/Cache/MmCache.php',
'className' => 'MmCache',
'import'    => array(LibDir.'/Util/Cache/ICache.php'),
'property'  => array(
    'expire'     => 1800,
	'configFile' => ConfigDir.'/memcache.config.php',
	'objRef'	 => array('encoding' => 'encoding'),
));
?>