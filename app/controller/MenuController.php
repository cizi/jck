<?php

namespace App\Controller;

use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\MenuRepository;
use Dibi\DateTime;
use Nette\Application\UI\Link;
use Nette\Application\UI\Presenter;

class MenuController {

	/** @var MenuRepository  */
	private $menuRepository;

	public function __construct(MenuRepository $menuRepository) {
		$this->menuRepository = $menuRepository;
	}

	/**
	 * @param string $lang
	 * @return array
	 */
	public function findMenuCategoriesForSelect($lang, $withEmpty = false) {
		if ($withEmpty) {
			$cats[0] = EnumerationRepository::NOT_SELECTED;
		} else {
			$cats = [];
		}

		$categories = $this->menuRepository->findAllItems();
		/** @var MenuEntity $category */
		foreach ($categories as $category) {
			if ($category->getLang() == $lang) {
				$cats[$category->getOrder()] = $category->getTitle();
			}
		}

		return $cats;
	}

	/**
	 * Recursively menu rendering in block content section
	 *
	 * @param Presenter $presenter
	 * @param MenuEntity[] $menuEntities
	 * @return string
	 */
	public function renderMenuItemWithSubItemsInBlockContent(Presenter $presenter, $menuEntities) {
		$tableData = "";
		/** @var MenuEntity $menuEntity */
		$counter = 0;
		foreach ($menuEntities as $menuEntity) {
			$blockForMenuItem = new Link($presenter, "BlockContent:ItemDetail", [$menuEntity->getOrder()]);

			$prefix = "";
			for ($i = 1; $i < $menuEntity->getLevel(); $i++) {
				$prefix .= " - ";
			}

			$tableData .= "
				<tr>
					<td>{$prefix}{$menuEntity->getOrder()}</td>
					<td>{$menuEntity->getTitle()}</td>
					<td>{$menuEntity->getLink()}</td>
					<td>{$menuEntity->getAlt()}</td>
					<td class='alignRight'>";

			$tableData .= "<a href='{$blockForMenuItem}' title='" . BLOCK_CONTENT_SETTINGS_BLOCKS_IN_MENU . "'>
					<span class='glyphicon glyphicon-circle-arrow-right colorGreen'></span></a></td></tr>";

			if ($menuEntity->hasSubItems()) {
				$anotherEntities = $this->menuRepository->findItems($menuEntity->getLang(),
					$menuEntity->getLevel() + 1, $menuEntity->getId());
				$tableData .= $this->renderMenuItemWithSubItemsInBlockContent($presenter, $anotherEntities);
			}
			$counter++;
		}

		return $tableData;
	}

	/**
	 * Recursively menu rendering
	 *
	 * @param Presenter $presenter
	 * @param MenuEntity[] $menuEntities
	 * @return string
	 */
	public function renderMenuItemWithSubItems(Presenter $presenter, $menuEntities) {
		$tableData = "";
		/** @var MenuEntity $menuEntity */
		$counter = 0;
		foreach ($menuEntities as $menuEntity) {
			$moveOrderUpLink = new Link($presenter, "Menu:MoveUp", [$menuEntity->getId()]);
			$moveOrderDownLink = new Link($presenter, "Menu:MoveDown", [$menuEntity->getId()]);
			$linkAddSubmenu = new Link($presenter, "Menu:Edit", [$menuEntity->getId(), null, $menuEntity->getLevel() + 1]);
			$linkEdit = new Link($presenter, "Menu:Edit", [$menuEntity->getId()]);
			$linkDelete = new Link($presenter, "Menu:Delete", [$menuEntity->getId()]);

			$prefix = "";
			for ($i=1; $i<$menuEntity->getLevel(); $i++) {
				$prefix .= " - ";
			}

			$tableData .= "
				<tr>
					<td>{$prefix}{$menuEntity->getOrder()}</td>
					<td>{$menuEntity->getTitle()}</td>
					<td>{$menuEntity->getLink()}</td>
					<td>{$menuEntity->getAlt()}</td>
					<td class='alignRight'>";
					if ($counter != 0) {
						// $tableData .= "<a href='{$moveOrderUpLink}' title='" . MENU_SETTINGS_MOVE_ITEM_UP . "'><span class='glyphicon glyphicon-chevron-up colorGrey'></span></a> &nbsp;&nbsp;";
					}
					if (($counter + 1) == count($menuEntities)) {
						$tableData .= "<div class='menuMoverPlaceholder'></div>";
					} else {
						// $tableData .= "<a href='{$moveOrderDownLink}' title='" . MENU_SETTINGS_MOVE_ITEM_DOWN . "'><span class='glyphicon glyphicon-chevron-down colorGrey'></span></a> &nbsp;&nbsp;";
					}

			$tableData .= "<a href='{$linkAddSubmenu}' title='" . MENU_SETTINGS_ADD_SUBITEM . "'><span class='glyphicon glyphicon-plus colorGreen'></span></a> &nbsp;&nbsp;
						<a href='{$linkEdit}' title='" . MENU_SETTINGS_EDIT_ITEM . "'}><span class='glyphicon glyphicon-pencil'></span></a> &nbsp;&nbsp;
						<a href='#' data-href='{$linkDelete}' class='colorRed' data-toggle='modal' data-target='#confirm-delete' title='" . MENU_SETTINGS_MENU_TOP_DELETE . "'><span class='glyphicon glyphicon-remove'></span></a>
					</td>
				</tr>
				";

			if ($menuEntity->hasSubItems()) {
				$anotherEntities = $this->menuRepository->findItems($menuEntity->getLang(), $menuEntity->getLevel() + 1, $menuEntity->getId());
				$tableData .= $this->renderMenuItemWithSubItems($presenter, $anotherEntities);
			}
			$counter++;
		}

		return $tableData;
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function prepareMenuItemsForEdit($id) {
		$menuItemsForEdit = $this->menuRepository->findForEditById($id);
		$level = "";
		$submenu = "";
		$result = [];
		foreach ($menuItemsForEdit as $menuItem) {
			$level = $menuItem->getLevel();
			$submenu = $menuItem->getSubmenu();

			$result[$menuItem->getLang()] = [
				'title' => $menuItem->getTitle(),
				'link' => $menuItem->getLink(),
				'alt' => $menuItem->getAlt(),
				'id' => $menuItem->getId()
			];
		}
		$result['level'] = $level;
		$result['submenu'] = $submenu;

		return $result;
	}

	public function renderMenuInFrontend($lang, Presenter $presenter, $level = 1, $id = null) {
		$menu = "";

		if ($id == null) {	// top menu entries
			$itemsInLevel = $this->menuRepository->findItems($lang, $level);
		} else {	// inner entries
			$itemsInLevel = $this->menuRepository->findSubItems($id, $lang, $level);
		}

		/** @var MenuEntity $item */
		foreach ($itemsInLevel as $item) {
			if ($item->hasSubItems()) {
				$menu .= (($level == 1) ? '<li class="dropdown">' : '<li class="dropdown-submenu">');

				$link = $presenter->link(":Frontend:Show:Category", ['lang' => $lang, 'id' => $item->getOrder(), 'seoText' => $item->getLink()]);
				$menu .= '<a href="#" class="dropdown-toggle menuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
				$menu .= '<i onclick="location.href=\''.$link.'\'" title="'.MENU_ALL_CATEGORY.'" class="fa fa-bars menuLinkAllCategory" aria-hidden="true"></i>' . $item->getTitle();

				if ($level == 1) {
					$menu .= '<span class="caret"></span>';
				}
				$menu .= '</a>';

				$menu .= '<ul class="dropdown-menu">';
				$menu .= $this->renderMenuInFrontend($lang, $presenter, $level + 1, $item->getId());
				$menu .= '</ul>';
				$menu .= '</li>';
			} else {
				$link = $presenter->link(":Frontend:Show:Category", ['lang' => $lang, 'id' => $item->getOrder(), 'seoText' => $item->getLink()]);
				$menu .= '<li><a href="'. $link .'" class="menuLink">' . $item->getTitle() . '</a></li>';
			}
		}
		if ($level == 1) {
			$link = $presenter->link(":Frontend:Calendar:Default", ['lang' => $lang]);
			$menu .= '<li><a href="' . $link . '" class="menuLink">' . MENU_ITEM_CALENDAR . '</a></li>';
		}

		/* working part of menu for example
		$menu2 = '<li><a href="#" class="menuLink">O společnoti</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle menuLink" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reference <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<!-- <li role="separator" class="divider"></li> -->
							<li><a href="#">Separated link</a></li>
							<li><a href="#">One more separated link</a></li>

							<li class="dropdown-submenu">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown</a>
								<ul class="dropdown-menu">
									<li><a href="#">Action</a></li>
									<li class="dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown</a>
										<ul class="dropdown-menu">
											<li class="dropdown-submenu">
												<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown</a>
												<ul class="dropdown-menu">
													<li><a href="#">Action</a></li>
													<li><a href="#">Another action</a></li>
													<li><a href="#">Something else here</a></li>
													<li><a href="#">Separated link</a></li>
													<li><a href="#">One more separated link</a></li>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				<li><a href="#" class="menuLink">Kontakt</a></li>';
		*/

		return $menu;
	}

	/**
	 * Rekurzivně projde menu a poskládá drobečkovou navigaci
	 * @param int $order
	 * @param string $lang
	 * @param Presenter $presenter
	 * @return array
	 */
	public function createBreadcrumbs($order, $lang, Presenter $presenter) {
		$breadcrumbsPath = [];

		$menuItem = $this->menuRepository->getMenuEntityByOrder($order, $lang);
		$link = $presenter->link(":Frontend:Show:Category", ['lang' => $lang, 'id' => $menuItem->getOrder(), 'seoText' => $menuItem->getLink()]);
		$breadcrumbsPath[$menuItem->getTitle()] = $link;
		if ($menuItem->hasPredecessor()) {
			$predecessor = $this->menuRepository->getMenuEntityById($menuItem->getSubmenu());
			$breadcrumbsPath = array_merge($breadcrumbsPath, $this->createBreadcrumbs($predecessor->getOrder(), $lang, $presenter));
		}

		return $breadcrumbsPath;
	}
}