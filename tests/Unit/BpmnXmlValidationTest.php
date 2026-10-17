<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class BpmnXmlValidationTest extends TestCase
{
    /**
     * Test that the PATEN end-to-end BPMN file exists and is valid BPMN 2.0 XML.
     */
    public function test_paten_bpmn_xml_is_valid_and_complete(): void
    {
        $filePath = dirname(__DIR__, 2).'/docs/bpmn/paten_business_process_e2e.bpmn';

        $this->assertFileExists($filePath, 'BPMN 2.0 file must exist in docs/bpmn/');

        $content = file_get_contents($filePath);
        $this->assertNotEmpty($content);

        // Verify valid XML structure
        $xml = new SimpleXMLElement($content);

        // Register namespaces
        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $xml->registerXPathNamespace('bpmndi', 'http://www.omg.org/spec/BPMN/20100524/DI');
        $xml->registerXPathNamespace('zeebe', 'http://camunda.org/schema/zeebe/1.0');

        // Check process node
        $processes = $xml->xpath('//bpmn:process[@id="Process_PATEN_E2E"]');
        $this->assertCount(1, $processes, 'Must contain Process_PATEN_E2E');
        $this->assertEquals('true', (string) $processes[0]['isExecutable']);

        // Check lanes: 4 lanes for Panitia, Publik, UMKM, Sistem
        $lanes = $xml->xpath('//bpmn:lane');
        $this->assertGreaterThanOrEqual(4, count($lanes), 'Must contain at least 4 swimlanes');

        // Check service tasks with Zeebe task definition
        $serviceTasks = $xml->xpath('//bpmn:serviceTask');
        $this->assertGreaterThanOrEqual(8, count($serviceTasks), 'Must contain all key service tasks');

        // Check Business Rule Task for Perbup Kutim guardrail
        $businessRuleTasks = $xml->xpath('//bpmn:businessRuleTask[@id="Task_GuardrailPerbupKutim"]');
        $this->assertCount(1, $businessRuleTasks, 'Must contain Perbup Kutim Guardrail business rule task');

        // Check Diagram Interchange elements
        $diagrams = $xml->xpath('//bpmndi:BPMNDiagram[@id="BPMNDiagram_PATEN"]');
        $this->assertCount(1, $diagrams, 'Must contain BPMNDiagram');
    }
}
