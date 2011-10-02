SilverStripe PanelAdmin Module
========================================

Maintainer Contacts
-------------------
*  Martijn van Nieuwenhoven (<info@axyrmedia.nl>)

Requirements
------------
* SilverStripe 2.4
* ModelAdmin

Usage Overview
--------------

Panel based ModelAdmin
Allows a Panelbased sidebar and custom ResultsField for DataObjects
See this slideshare for what it van do:

http://www.slideshare.net/marvanni/panelmodeladmin-example

See the productadmin module for an usage example:
http://svn.axyrmedia.nl/productadmin/

Known Issues
------------

Sometimes the wrong buttons are displayed in the ResultsForm and EditForm

When using the CategoryPanel, you need to set the Parent Class in you Child DataObject as follows:

static $admin_parent_class = 'SomeParentClass';

static $searchable_fields = array(
		
	'SomeField',
		
	'SomeOtherField',
		
	'SomeParentClassID'

); 