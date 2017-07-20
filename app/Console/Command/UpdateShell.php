<?php

class UpdateShell extends AppShell
{
	// the models to use
	public $uses = array('Vector', 'Hostname', 'Ipaddress');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Update Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Update Shell runs all needed jobs to update production\'s database.'));
		
		$parser->addSubcommand('vector_scan', array(
			'help' => __d('cake_console', 'Scans vectors and makes sure they are associated with a hostnames/ipaddresses listing.'),
			'parser' => array(
				'arguments' => array(
//					'minutes' => array('help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'))
				)
			)
		));
		
		return $parser;
	}
	
	public function vector_scan()
	{
	/*
	 * Scans vectors and makes sure they are associated with a hostnames/ipaddresses listing.
	 * 
	 */
		
		// Hostnames
		$vectors = $this->Vector->typeList('hostname', false, true);
		$this->out($this->Vector->shellOut());
		
		// add the new records
		$results = $this->Hostname->checkAddBlank(array_keys($vectors));
		$this->out($this->Hostname->shellOut());
		
		// Ip Addresses
		$vectors = $this->Vector->typeList('ipaddress', false, true);
		$this->out($this->Vector->shellOut());
		
		// add the new records
		$results = $this->Ipaddress->checkAddBlank(array_keys($vectors));
		$this->out($this->Ipaddress->shellOut());
	}
}
?>