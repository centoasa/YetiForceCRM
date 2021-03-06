<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

/**
 * PriceBooks Record Model Class
 */
class PriceBooks_Record_Model extends Vtiger_Record_Model
{

	/**
	 * Function return the url to fetch List Price of the Product for the current PriceBook
	 * @return string
	 */
	public function getProductUnitPriceURL()
	{
		$url = 'module=PriceBooks&action=ProductListPrice&record=' . $this->getId();
		if (!$this->isEmpty('src_record')) {
			$url .= '&itemId=' . $this->get('src_record');
		}
		return $url;
	}

	/**
	 * Function returns the List Price for PriceBook-Product/Service relation
	 * @param <Integer> $relatedRecordId - Product/Service Id
	 * @return <Integer>
	 */
	public function getProductsListPrice($relatedRecordId)
	{

		return (new \App\Db\Query())->select(['listprice'])
				->from('vtiger_pricebookproductrel')
				->where(['pricebookid' => $this->getId(), 'productid' => $relatedRecordId])
				->scalar();
	}

	/**
	 * Function updates ListPrice for PriceBook-Product/Service relation
	 * @param <Integer> $relatedRecordId - Product/Service Id
	 * @param <Integer> $price - listprice
	 */
	public function updateListPrice($relatedRecordId, $price)
	{
		$isExists = (new \App\Db\Query())->from('vtiger_pricebookproductrel')->where(['pricebookid' => $this->getId(), 'productid' => $relatedRecordId])->exists();
		if ($isExists) {
			App\Db::getInstance()->createCommand()
				->update('vtiger_pricebookproductrel', ['listprice' => $price], ['pricebookid' => $this->getId(), 'productid' => $relatedRecordId])
				->execute();
		} else {
			App\Db::getInstance()->createCommand()
				->insert('vtiger_pricebookproductrel', [
					'pricebookid' => $this->getId(),
					'productid' => $relatedRecordId,
					'listprice' => $price,
					'usedcurrency' => $this->get('currency_id')
				])->execute();
		}
	}

	/**
	 * Function deletes the List Price for PriceBooks-Product/Services relationship
	 * @param <Integer> $relatedRecordId - Product/Service Id
	 */
	public function deleteListPrice($relatedRecordId)
	{
		return App\Db::getInstance()->createCommand()
				->delete('vtiger_pricebookproductrel', ['pricebookid' => $this->getId(), 'productid' => $relatedRecordId])
				->execute();
	}

	public function saveToDb()
	{
		parent::saveToDb();
		$this->updateListPrices();
	}

	public function updateListPrices()
	{
		\App\Log::trace('Entering function updateListPrices...');
		$pricebookCurrency = $this->get('currency_id');
		$dataReader = (new App\Db\Query())->from('vtiger_pricebookproductrel')
				->where(['and', ['pricebookid' => $this->getId()], ['<>', 'usedcurrency', $pricebookCurrency]])
				->createCommand()->query();
		while ($row = $dataReader->read()) {
			$productCurrencyInfo = \vtlib\Functions::getCurrencySymbolandRate($row['usedcurrency']);
			$pricebookCurrencyInfo = \vtlib\Functions::getCurrencySymbolandRate($pricebookCurrency);
			$computedListPrice = $row['listprice'] * $pricebookCurrencyInfo['rate'] / $productCurrencyInfo['rate'];
			App\Db::getInstance()->createCommand()
				->update('vtiger_pricebookproductrel', ['listprice' => $computedListPrice, 'usedcurrency' => $pricebookCurrency], ['pricebookid' => $this->getId(), 'productid' => $row['productid']])
				->execute();
		}
		$dataReader->close();
		\App\Log::trace('Exiting function updateListPrices...');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRecordRelatedListViewLinksLeftSide(Vtiger_RelationListView_Model $viewModel)
	{
		$links = parent::getRecordRelatedListViewLinksLeftSide($viewModel);
		if ($viewModel->getRelationModel()->isEditable() && $this->isEditable()) {
			$links['LBL_EDIT'] = Vtiger_Link_Model::getInstanceFromValues([
					'linklabel' => 'LBL_EDIT',
					'linkicon' => 'glyphicon glyphicon-pencil',
					'linkclass' => 'btn-xs btn-default editListPrice cursorPointer',
					'linkdata' => ['url' => 'index.php?module=PriceBooks&view=ListPriceUpdate&record=' . $viewModel->getParentRecordModel()->getId() . '&relid=' . $this->getId() . '&currentPrice=' . $this->get('listprice'), 'related-recordid' => $this->getId(), 'list-price' => $this->get('listprice')]
			]);
		}
		return $links;
	}
}
