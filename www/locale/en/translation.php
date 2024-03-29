<?php

// chyba, požadavek na neexitující stránku
define("APP_CONTENT_ERROR", 'Page Not Found');
define("APP_CONTENT_ERROR_INFO", 'The page you requested could not be found. It is possible that the address is incorrect, or that the page no longer exists. Please use a search engine to find what you are looking for.');
define("APP_CONTENT_ERROR_SIGN", 'error');

// formulář login do administrace
define("ADMIN_LOGIN_HEADER", 'Login');
define("ADMIN_LOGIN_EMAIL", 'Email');
define("ADMIN_LOGIN_EMAIL_REQ", 'Please fill your login.');
define("ADMIN_LOGIN_PASS", 'Password');
define("ADMIN_LOGIN_PASS_REQ", 'Please fill your password');
define("ADMIN_LOGIN_LANG", 'Language of inteface');
define("ADMIN_LOGIN_REMEMBER_ME", 'Remember me');
define("ADMIN_LOGIN_LOGIN", 'Login');
define("ADMIN_LOGIN_EMAIL_PLACEHOLDER", 'Email');
define("ADMIN_LOGIN_PASS_PLACEHOLDER", 'Password');
define("ADMIN_LOGIN_FAILED", 'Incorrect login name or password');
define("ADMIN_LOGIN_UNLOGGED", 'You have been successfully logged out.');
define("ADMIN_LOGIN_FORGOTTEN_PASSWORD", 'Forgotten password?');
define("ADMIN_LOGIN_RESET_PASSWORD", 'Reset password');
define("ADMIN_LOGIN_RESET_PASSWORD_EMAIL_FAIL", 'Email does not exist in database.');
define("ADMIN_LOGIN_RESET_SUCCESS", 'Password has benn successfully changed and send to your email.');
define("ADMIN_LOGIN_RESET_FAILED", 'Password reset failed. Try again later.');
define("ADMIN_LOGIN_PASSWORD_CHANGED_EMAIL_SUBJECT", 'Password changed in %s');
define("ADMIN_LOGIN_PASSWORD_CHANGED_EMAIL_BODY", "Hello, <br /> your password has been changed in %s. <br /><br /> New password is '%s'.<br /><br />");
define("ADMIN_LOGIN_REAL_NAME", "Name and surname");
define("ADMIN_LOGIN_REAL_NAME_REQ", "PLease, fill your real name!");
define("ADMIN_LOGIN_PHONE", "Phone");

// položky menu
define("MENU_TITLE", 'Main menu');
define("MENU_DASHBOARD", 'Dashboard');
define("MENU_HEADER", 'Page header');
define("MENU_LOGO", 'Slider');
define("MENU_MENU", 'Page menu');
define("MENU_BLOCK", 'Page content blocks');
define("MENU_ARTICLE", 'Articles');
define("MENU_MENU_BLOCK", 'Menu vs blocks');
define("MENU_CONTACT_FORM", 'Contact form');
define("MENU_FOOTER", 'Page footer');
define("MENU_USERS", 'Users');
define("MENU_SETTINGS", 'Page setting');
define("MENU_BANNER", 'Banners');
define("MENU_ENUMERATION", 'Enumerations');
define("MENU_LANG", 'Language setting');
define("MENU_LOGOUT", 'Logout');
define("MENU_ITEM_CALENDAR", 'Calendar');
define("MENU_ALL_CATEGORY", 'Show all category');

// dashboard
define("DASH_EMAIL", 'Saved emails');

// hledání na hlavní stránce
define("MAIN_SEARCH_FROM", 'From');
define("MAIN_SEARCH_FROM_REQ", 'Date from is mandatory!');
define("MAIN_SEARCH_TO", 'To');
define("MAIN_SEARCH_TODAY", 'Today');
define("MAIN_SEARCH_TOMORROW", 'Tomorrow');
define("MAIN_SEARCH_WEEKEND", 'Weekend');
define("MAIN_SEARCH_SEARCH", 'Search');
define("MAIN_SEARCH_HEADER", 'Date');
define("MAIN_SEARCH_REQ_FIELDS", 'No search criteria filled!');

// vlastní akce
define("SUBMIT_OWN_EVENT_HEADER", 'Submit own event');
define("SUBMIT_OWN_EVENT", 'Submit own event');
define("SUBMIT_OWN_EVENT_TEXT", 'If you would like an event to be considered for inclusion, please fill in the form below, making sure you provide 
all the information requested. This will be passed on to our editorial team. Please note this form is for events only. Any other content - such as 
tours, airport facilities or attractions - should be submitted using our feedback form.');
define("SUBMIT_OWN_BUTTON", 'Send for approval');
define("SUBMIT_OWN_SAVED", 'Event has benn send for submitting');
define("SUBMIT_OWN_FAILED", 'Event saved failed, please try again later');
define("SUBMIT_OWN_MAIL_SUBJECT", 'JCK - new FREE EVENT has been inserted');
define("SUBMIT_OWN_MAIL_BODY", 'Hello, <br /><br />new event has been inserted from the web form. Please login in the admin section for show this new event here %s . <br /><br /> Admin JCK');

// číselníky
define("ENUM_TITLE", 'Enumeration setting');
define("ENUM_TITLE_DESCRIPTION", 'Zde je možné spravovat číselníky, které jsou napříč celým systémem. Správu by měl dělat jen administrátor,
								protože změny mohou mít dopad na celou aplikaci');
define("ENUM_TABLE_ENUM_NAME", 'Enumeration title');
define("ENUM_TABLE_FIELDS_PREVIEW", 'Enumeration items');
define("ENUM_TABLE_ADD_NEW_ENUM", 'Add enumeration');
define("ENUM_TABLE_ENUM_EDIT", 'Enumeration edit');
define("ENUM_TABLE_ENUM_DELETE", 'Enumeration delete');
define("ENUM_TABLE_ENUM_WARNING_TITLE", 'Deleting enumeration');
define("ENUM_TABLE_ENUM_WARNING_TEXT", 'Are really want to delete whole enumeration item?');

define("ENUM_EDIT_TITLE", 'Enumeration detail');
define("ENUM_EDIT_DESC", 'Enumeration detail area, please take care what are you doing');
define("ENUM_EDIT_NAME", 'Enumeration title');
define("ENUM_EDIT_NAME_REQ", 'Title of enumeration is mandatory');
define("ENUM_EDIT_ITEMS_LABEL", 'Enumeration items');
define("ENUM_EDIT_ITEM_EDIT", 'Editation of enumeration item');
define("ENUM_EDIT_ITEM_DELETE", 'Enum item delete');
define("ENUM_EDIT_ITEM_ADD", 'Add enumeration item');
define("ENUM_EDIT_ITEM_DELETE_MSG", 'Are you really want to delete enumeration item');

define("ENUM_EDIT_ITEM_DESC",  'Edit of enumeration item in all langugages mutations.');
define("ENUM_EDIT_ITEM_TITLE", 'Item of enumeration');
define("ENUM_EDIT_ITEM_NAME", 'Value of enumeration item');
define("ENUM_EDIT_ITEM_NAME_REQ", 'Value of enumeration item is mandatory.');
define("ENUM_EDIT_ITEM_SAVE", 'Value of enumeration item has been successfully saved.');
define("ENUM_EDIT_ITEM_FAIL", 'Saving enumeration item failed.');
define("ENUM_DELETE_SUCCESS", 'Whole enumeration has benn deleted.');
define("ENUM_DELETE_FAIL", 'During whole enumeration failed. Probably any value of enumeration item is in used. ');
define("ENUM_DELETE_ITEM_FAIL", 'Enumeration item deletation failed. Probably the value is in use. ');

// příspěvky
define("ARTICLE_HEADER", 'Articles');
define("ARTICLE_HEADER_DETAIL", 'Article detail');
define("ARTICLE_HEADER_INFO", 'In article section you are able to add, edit or delete content of article. Each article is inserted in the category which means event or text plain article Article is text plain content, event has to have date or dates of taking place. ');
define("ARTICLE_TYPE", 'Type of article');
define("ARTICLE_CATEGORY", 'Category');
define("ARTICLE_PROGRAM", 'Program');
define("ARTICLE_ACTIVE", 'Active');
define("ARTICLE_BY", 'Inserted by');
define("ARTICLE_TIMESTAMP", 'Inserted');
define("ARTICLE_CONTENT", 'Content');
define("ARTICLE_VALIDITY", 'Validity');
define("ARTICLE_PLACE", 'Place');
define("ARTICLE_PLACE_TEXT", 'Title of taking place');
define("ARTICLE_CONTACT_EMAIL", 'Contact email');
define("ARTICLE_CONTACT_EMAIL_REQ", 'Contact email is mandatory');
define("ARTICLE_CONTACT_EMAIL_FORMAT", 'Invalid email format');
define("ARTICLE_ADDRESS", 'Address');
define("ARTICLE_LOCATION", 'Location');
define("ARTICLE_SUBLOCATION", 'City');
define("ARTICLE_CONTACT", 'Contact');
define("ARTICLE_CONTENT_HEADER", 'Title of event');
define("ARTICLE_CONTENT_HEADER_REQ", 'Artcile content header is mandatory!');
define("ARTICLE_CONTENT_CONTENT", 'Event description');
define("ARTICLE_CONTENT_CONTENT_REQ", 'Article content is mandatory!');
define("ARTICLE_CONTENT_SEO", 'SEO');
define("ARTICLE_MAIN_PIC", 'Master pic');
define("ARTICLE_MAIN_URL", 'Master pic (660 x 443px)');
define("ARTICLE_MAIN_URL_REQ", 'Master pic (660 x 443px) is mandatory field.');
define("ARTICLE_GALLERY", 'Gallery');
define("ARTICLE_MAIN_DOCS", 'Files (posters, program, etc.)');
define("ARTICLE_URL", 'Web URL of event');
define("ARTICLE_FB_URL", 'FB event URL');
define("ARTICLE_YT_URL", 'Youtube URL');
define("ARTICLE_SAVE_FAILED", 'Article saved failed. ');
define("ARTICLE_CONTENT_REWRITE", 'Copy content from CS to EN');
define("ARTICLE_CONTENT_CONFIRM", 'Save article');
define("ARTICLE_CALENDAR", 'event calendar');
define("ARTICLE_DUPLICATE", 'Duplicate article');
define("ARTICLE_DUPLICATE_OK", 'Duplicate has been successfully created');
define("ARTICLE_DUPLICATE_FAILED", 'Duplicate creation failed');
define("ARTICLE_EDIT", 'Edit article');
define("ARTICLE_DELETE", 'Delete article');
define("ARTICLE_MODAL_HEADER", 'Article deletation');
define("ARTICLE_MODAL_TEXT", 'Are you really want do delete article with its language mutations and schedule?');
define("ARTICLE_VIEWS_COUNT", 'Count of views');
define("ARTICLE_DATE_FROM", 'Start date');
define("ARTICLE_DATE_TO", 'End date');
define("ARTICLE_START_TIME", 'Time [hours:minutes]');
define("ARTICLE_ADD_TIMETABLE", 'Add new date/time of event');
define("ARTICLE_ADD_PICS_TO_GALLERY", 'Add pictures to shared gallery');
define("ARTICLE_REMOVE_TIMETABLE", 'Remove timetable');
define("ARTICLE_TIMETABLE_TIME_WRONG_FORMAT", 'Invalid time format!');
define("ARTICLE_TIMETABLE_START_DATE_MISSING", 'Invalid start date!');
define("ARTICLE_PAGINATOR_NEXT", 'Next');
define("ARTICLE_PAGINATOR_PREVIOUS", 'Previous');
define("ARTICLE_PAGINATOR_FIRST", 'First');
define("ARTICLE_PAGINATOR_LAST", 'Last');
define("ARTICLE_PAGINATOR_PAGE", 'Page');
define("ARTICLE_PAGINATOR_FROM", 'from');
define("ARTICLE_VIEW_DETAIL", 'View detail');
define("ARTICLE_TOP_EVENTS", 'Top events');
define("ARTICLE_EVENT_INFO", 'Info');
define("ARTICLE_EVENT_CITIES", 'City');
define("ARTICLE_EVENT_CITIES_TIP", 'Place tips');
define("ARTICLE_EVENT_PLACES", 'Place');
define("ARTICLE_PARTNER_PLACES", "Partner's places");
define("ARTICLE_EVENT_DOCS", 'Files');
define("ARTICLE_EVENT_PLACE_EVENTS", 'Events');
define("ARTICLE_EVENT_PLACE_ARTICLES", 'Articles');
define("ARTICLE_TRIGGER_FILTER", 'Filter');
define("ARTICLE_ACTIVITY_BOTH", 'Active / inactive');
define("ARTICLE_ACTIVITY_INACTIVE", 'Inactive');
define("ARTICLE_ACTIVITY_ACTIVE", 'Active');
define("ARTICLE_FACEBOOK", 'Facebook');
define("ARTICLE_PROMOTER", 'Web');
define("ARTICLE_YOUTUBE", 'Youtube');
define("ARTICLE_MAP", 'Map');
define("ARTICLE_TAKING_MISSING", 'Please add at least one date and time of the event.');
define("ARTICLE_NOT_FOUND", 'Content has not been found.');
define("ARTICLE_CALENDAR_DIRECT", 'Events on date %s ');

// měsíce
define("JANUARY", 'January');
define("FEBRUARY", 'February');
define("MARCH", 'March');
define("APRIL", 'April');
define("MAY", 'May');
define("JUNE", 'June');
define("JULY", 'July');
define("AUGUST", 'August');
define("SEPTEMBER", 'September');
define("OCTOBER", 'October');
define("NOVEMBER", 'November');
define("DEMEBER", 'December');

define("MON", 'Monday');
define("THU", 'Tuesday');
define("WED", 'Wednesday');
define("THR", 'Thursday');
define("FRI", 'Friday');
define("SAT", 'Saturday');
define("SUN", 'Sunday');

// galerie
define("GALLERY_TITLE", 'Gallery');
define("GALLERY_TITLE_DESC", 'Gallery management');
define("GALLERY_ADD_NEW", 'New gallery');
define("GALLERY_NAME", 'Title');
define("GALLERY_NAME_REQ", 'Title is mandatory!');
define("GALLERY_DESC", 'Description');
define("GALLERY_DESC_REQ", 'Description is mandatory!');
define("GALLERY_ON_MAIN_PAGE", 'Show on main page');
define("GALLERY_ACTIVE", 'Active');
define("GALLERY_PICS", 'Images');
define("GALLERY_PIC_COUNT", 'Image count');
define("GALLERY_EDIT", 'Edit gallery');
define("GALLERY_EDITATION", 'Gallery editation');
define("GALLERY_EDIT_PAGE", 'Gallery edit');
define("GALLERY_DELETE", 'Gallery delete');
define("GALLERY_DELETATION", 'Gallery deletation');
define("GALLERY_DELETATION_TEXT", 'Are you really want to delete the gallery?');
define("GALLERY_ADD_PICS_AND_BACK", 'Upload images');
define("GALLERY_SAVE_GALLERY", 'Save gallery');

define("PIC_SAVE_GALLERY", 'Save images');
define("PIC_DELETE_PICS", 'Delete selected images');
define("PIC_DELETE_TITLE", 'Delete selected images');
define("PIC_DELETE_TEXT", 'Are you really want to delete selected images?');
define("PIC_DELETE_CANCEL", 'Cancel');
define("PIC_DELETE_DELETE", 'Delete');
define("PIC_DELETED_EXISTS", 'Below selected images are in used, is not possible to delete them.');

define("CHOSEN_PICKUP", 'Choose an option');
define("CHOSEN_NO_MATCH", 'No match');
define("USER_REQUEST_NOT_PRIV", 'Permission denied for this request.');
define("PIC_NOT_POSSIBLE_DELETE", 'File is not possible to delete. The picture is probably in use. ');
define("PIC_DELETE_DELETED", 'File has been successfully deleted. ');

// homepage
define("HP_ARTICLE_TEXT", 'Latest articles');
define("HP_ARTICLE_GALLERY", 'Latest galleries');

// bannery
define("BANNER_TITLE", 'Banners settings');
define("BANNER_TITLE_DETAIL", 'Banner detail');
define("BANNER_TITLE_INFO", 'Here you are able to manage banners. ');
define("BANNER_TITLE_ITEM", 'Title');
define("BANNER_TITLE_ITEM_REQ", 'Title field is mandatory!');
define("BANNER_TYPE", 'Banner type');
define("BANNER_CATEGORIES", 'Category');
define("BANNER_START_DATE", 'Start date');
define("BANNER_START_DATE_REQ", 'Start date is mandatory!');
define("BANNER_END_DATE", 'Date from');
define("BANNER_SHOW_MAIN_PAGE", 'Show on main page');
define("BANNER_SHOW_COUNTER", 'Showed');
define("BANNER_CLICK_COUNTER", 'Clicked');
define("BANNER_CLICK_EDIT", 'Edit banner');
define("BANNER_CLICK_DELETE", 'Delete banner');
define("BANNER_CLICK_DELETE_MSG", 'Are you really want to delete the banner?');
define("BANNER_EDIT_CONFIRM", 'Save banner');
define("BANNER_IMAGE", 'Banner image');
define("BANNER_ACTION_URL", 'Link');
define("BANNER_SAVED_FAILED", 'Banner setting save failed');
define("BANNER_DELETE_OK", 'Banner has been successfully deleted');
define("BANNER_DELETE_FAILED", 'An error occurred during banner deletation');

// hledání
define("MAIN_PAGE_SEARCH", 'Search');
define("MAIN_PAGE_SEARCH_REQ", 'Please, fill the search phase!');
define("MAIN_PAGE_DESTINATION", 'Where');
define("MAIN_PAGE_SHARE", 'Share');

// admin - users list
define("USER_TITLE", 'Users');
define("USER_INFO_TEXT", "Here you can manage users and their roles. Login name is their email addresses. It assures the unique user name (login).
						Please remember that these changes are undone!");
define("USER_TABLE_HEADER_LOGIN", "Login");
define("USER_TABLE_HEADER_ROLE", "Role");
define("USER_TABLE_HEADER_ACTIVE", "Active");
define("USER_TABLE_HEADER_LAST_LOGIN", "Last login");
define("USER_TABLE_HEADER_REGISTERED_DATE", "Date of registration");
define("USER_DELETED", "User has been deleted");
define("USER_ADDED", "User added");
define("USER_EDITED", "User data updated");
define("USER_DELETED_FAILED", "Users has not been deleted, please repeat action later.");
define("USER_DELETE", 'Delete user');
define("USER_EDIT", 'Edit user');
define("USER_ADD_USER", 'Add new user');
define("USER_CONFIRM_DELETE_TITLE", 'User deletions');
define("USER_CONFIRM_DELETE", 'Really delete user?');
define("USER_CONFIRM_DELETE_CANCEL", 'Cancel');
define("USER_CONFIRM_DELETE_OK", 'Delete');
define("USER_ERROR_ACTIVE_SWITCH", 'Communication with server broken, please repeat action later.');
// admin - user edit
define("USER_EDIT_FORM_ADD", ' Addition the new user');
define("USER_EDIT_FORM_EDIT", 'Editing of user %s');
define("USER_EDIT_EMAIL_LABEL", 'Email');
define("USER_EDIT_EMAIL_REQ", 'Email field is mandatory!');
define("USER_EDIT_EMAIL_VALIDATION", 'Please fill the valid email address!');
define("USER_EDIT_PASS_LABEL", 'Password');
define("USER_EDIT_PASS_REQ", 'Password field is mandatory!');
define("USER_EDIT_PASS_AGAIN_LABEL", 'Password confirmation');
define("USER_EDIT_PASS_AGAIN_REQ", 'Password confirmation is mandatory!');
define("USER_EDIT_ROLE_LABEL", 'Role');
define("USER_EDIT_ACTIVE_LABEL", 'Active');
define("USER_EDIT_SAVE_BTN_LABEL", 'Save');
define("USER_EDIT_SITEMAP_BTN_LABEL", 'Generate sitemap files');
define("USER_EDIT_SITEMAP_AVAIL", 'Available sitemap.xml file');
define("USER_EDIT_SITEMAP_GENERATION_DONE", 'Sitemap.xml file has been successfully generated');
define("USER_EDIT_SITEMAP_BTN_INFO", 'It will generate file(s) sitemap for search engines. For each language mutation will be generate one sitemap file.');
define("USER_EDIT_BACK_BTN_LABEL", 'Cancel');
define("USER_EDIT_SAVE_FAILED", 'Changes has not been processed, please repeat action later');
define("USER_SEARCH_FIELD", "Fulltext searching in users");
define("USER_SEARCH_BUTTON", "Search");

// user roles - select
define("USER_ROLE_LAYOUT_CHANGER", "Full access");
define("USER_ROLE_CONTENT_CHANGER", "Editor");
define("USER_ROLE_GUEST", "Host");
define("USER_ROLE_ADMINISTRATOR", "Administrator");

// webconfig
define("WEBCONFIG_WEBMUTATION", "Language mutation");
define("WEBCONFIG_WEBMUTATION_INFO", "Current edition will be used for current language mutation. If you have more language mutations is necessary to to do it for all mutations.");
define("WEBCONFIG_TITLE", "Page config");
define("WEBCONFIG_TITLE_INFO", "Here you can configure the web layout, width or Google analytics code.");
define("WEBCONFIG_WEB_NAME", "Web title");
define("WEBCONFIG_WEB_NAME_INFO", "This title will be displayed in the main window in browser. Is important for search engines.");
define("WEBCONFIG_WEB_KEYWORDS", "Keywords");
define("WEBCONFIG_WEB_KEYWORDS_INFO", "Each keyword separate with comma (,).
										<b>Note:</b> keywords in tag keywords are ignorated by robots but even though is better to fill them.");

define("WEBCONFIG_WEB_WIDTH", "Page width");
define("WEBCONFIG_WEB_WIDTH_INFO", "It sets page width in the browser. This item does not have impact on web responsivity.");
define("WEBCONFIG_WEB_FAVICON", "Page icon");
define("WEBCONFIG_WEB_FAVICON_INFO", "Icon will be displayed on address bar of web browser or favorite list.
										Icon has to have special rules. Type of image should be ico and should have 16x16 pixel as resolution.");
define("WEBCONFIG_WEB_FAVICON_FORMAT", "Icon ust be ico (format ICO)!");
define("WEBCONFIG_WEB_GOOGLE_ANALYTICS", "Google Analytics");
define("WEBCONFIG_WEB_GOOGLE_ANALYTICS_INFO", "Google Analytics helps you to monitor page visits your pages,
												count time how long was the visitor on the page etc. Google Analytics is javascript code which is pasted on each page.
												More information you can find here: <a target='_blank' href='https://www.google.com/analytics/'>https://www.google.com/analytics/</a>");
define("WEBCONFIG_WEB_SAVE_SUCCESS", "Changes have benn successfully saved");
define("WEBCONFIG_WEB_BACKGROUND_COLOR", "Background colour");
define("WEBCONFIG_WEB_BACKGROUND_COLOR_INFO", "Here you can choose the background colour of your pages. Color will be used for the all background.");
define("WEBCONFIG_WEB_MENU_SHOW", "Show menu");
define("WEBCONFIG_WEB_MENU_SHOW_INFO", "It sets if the menu will be visible or not. ");
define("WEBCONFIG_WEB_MENU_BACKGROUND_COLOR", "Background colour of the menu");
define("WEBCONFIG_WEB_MENU_BACKGROUND_COLOR_INFO", "It sets colour of the menu bar.");
define("WEBCONFIG_WEB_MENU_LINK_COLOR", "Font colour in the menu");
define("WEBCONFIG_WEB_MENU_LINK_COLOR_INFO", "It sets link colour in the menu bar.");

define("WEBCONFIG_SETTINGS_SHOW_HOME", 'Show home link');
define("WEBCONFIG_SETTINGS_SHOW_HOME_INFO", 'On the first position in the menu bar will be showed small house which will be the link to homepage.');
define("WEBCONFIG_SETTINGS_SHOW_BLOCK", 'Block of the hamopage');
define("WEBCONFIG_SETTINGS_SHOW_BLOCK_INFO", 'Choose the content block which will be showed on the homepage.');
define("WEBCONFIG_SETTINGS_LANG_DEPENDS", 'Settings depend on the language');
define("WEBCONFIG_SETTINGS_LANG_COMMON", 'General settings');

// modal window
define("MODAL_BUTTON_OK", 'OK');
define("MODAL_WINDOWS_WARNING_TITLE", 'Warning');

// slider
define("SLIDER_SETTINGS", "Picture setting (slider)");
define("SLIDER_SETTINGS_INFO", "Slider can have one or more pictures which will be randomly changing in the top of the pages. <br />
								<b>Important: </b> If will saved more then one picture is <b>necessary</b> to have the sema resolution for each picture.");
define("SLIDER_SETTINGS_PICS", "Save slider image");
define("SLIDER_SETTINGS_PICS_INFO", "Images are possible to save by one by one or more in one batch.");
define("SLIDER_SETTINGS_CURRENT_PICS", "Current slider images");
define("SLIDER_SETTINGS_SAVE_BTN_LABEL", 'Save');
define("SLIDER_SETTINGS_PIC_FORMAT", "BMP, JPG, PNG images are supported. Please load just these formats.");
define("SLIDER_SETTINGS_CONFIRM_MODAL_DELETE_TITLE", 'Delete slider image');
define("SLIDER_SETTINGS_CONFIRM_MODAL_DELETE_MSG", 'Are you really want to delete slider image?');
define("SLIDER_SETTINGS_CONFIRM_MODAL_OK", 'Delete');
define("SLIDER_SETTINGS_CONFIRM_MODAL_CANCEL", 'Cancel');
define("SLIDER_SETTINGS_DELETE_TITLE", 'Image deletions');
define("SLIDER_SETTINGS_SLIDER_ACTIVE_LABEL", 'Turn-on slider');
define("SLIDER_SETTINGS_SLIDER_ACTIVE_INFO", 'Switches the slider in the top of the pages.');
define("SLIDER_SETTINGS_SLIDER_SLIDING_LABEL", 'Turn-on slide show');
define("SLIDER_SETTINGS_SLIDER_SLIDING_INFO", 'If will be the slide show turned on images will be changing in the time interval you want.
 											If the slide show stays off, will be displayed statically one image. But if will be in slider saved (loead)
 											more then one image it causes that after page reloed will be display another image.');
define("SLIDER_SETTINGS_SLIDER_ARROWS_LABEL", 'Show slider control');
define("SLIDER_SETTINGS_SLIDER_ARROWS_INFO", 'Show arrows in the slider which can be used for swithcing images in the slider.');

define("SLIDER_SETTINGS_SLIDER_WITDH", 'Slider width');
define("SLIDER_SETTINGS_SLIDER_WITDH_INFO", 'It sets width of the slider to page content. <br />
											100% = means all the page (browser) width<br />
											50%  = means half of the page (browser) width <br />
											<b>Note:</b> 100% is default is recommanded for most users');
define("SLIDER_SETTINGS_TIMING", 'Slider time interval (s)');
define("SLIDER_SETTINGS_TIMING_INFO", 'It means number of second before each slide.');
define("SLIDER_SETTINGS_SAVE_OK", "Changes have been successfully saved.");


// menu
define("MENU_SETTINGS_TITLE", 'Menu configuration');
define("MENU_SETTINGS_INFO", 'Here you are able to configure menu and its items and subitems. It is not recommended to do more then one level of nesting. The reason is mobile devices such a phone
							and table where more then one nesting is uncomfortable to browse. ');
define("MENU_SETTINGS_ITEM_NAME", 'Item title');
define("MENU_SETTINGS_ITEM_NAME_REQ", 'Item title is mandatory!');
define("MENU_SETTINGS_ITEM_LINK", 'URL link');
define("MENU_SETTINGS_ITEM_LINK_REQ", 'URL link is mandatory!');
define("MENU_SETTINGS_ITEM_SEO", 'SEO title');
define("MENU_SETTINGS_ITEM_LINK_ADDED", 'Item has been successfully added');
define("MENU_SETTINGS_ITEM_DELETED", 'Item has been successfully deleted');
define("MENU_SETTINGS_ITEM_LINK_FAILED", 'During saving appeared a problem. Please repeat it later');
define("MENU_SETTINGS_ITEM_LINK_EXISTS", 'Your desired link (%s) already exists in the menu. Please, choose a unique value.');
define("MENU_SETTINGS_ITEM_LINK_INFO", 'Title in menu URL item . <b>IMPORTANT for SEO</b>.');
define("MENU_SETTINGS_SUBMENU", 'Has subitems');
define("MENU_SETTINGS_ADD", 'Add item');
define("MENU_SETTINGS_TABLE_ORDER", 'Order in menu');
define("MENU_SETTINGS_LINK", 'Link name in URL');
define("MENU_SETTINGS_IN_MENU_TITLE", 'Title in menu');
define("MENU_SETTINGS_ALT", 'SEO title');
define("MENU_SETTINGS_MENU_TOP_DELETE", 'Delete menu item');
define("MENU_SETTINGS_EDIT_ITEM", 'Edit menu item');
define("MENU_SETTINGS_ADD_SUBITEM", 'Add subitem in menu item');
define("MENU_SETTINGS_MOVE_ITEM_UP", 'Move up');
define("MENU_SETTINGS_MOVE_ITEM_DOWN", 'Move down');
define("MENU_SETTINGS_CONFIRM_MODAL_DELETE_TITLE", 'Menu item deletions');
define("MENU_SETTINGS_CONFIRM_MODAL_DELETE_MSG", 'Are you really want to delete the item?');
define("MENU_SETTINGS_ITEM_MOVE_UP", 'Menu item has benn successfully moved up.');
define("MENU_SETTINGS_ITEM_MOVE_DOWN", 'Menu item has benn successfully moved down.');
define("MENU_SETTINGS_ITEM_MOVE_FAILED", 'Error during menu item edition, please try it again later.');
define("MENU_SETTINGS_ITEM_TITLE", 'Menu item configuration');
define("MENU_SETTINGS_ITEM_INFO", 'Please fill the parameters for menu item. SEO title should have been apt for the given link. ');

// constact form
define("CONTACT_FORM_SETTING_TITLE", 'Contact form setting');
define("CONTACT_FORM_SETTING_TITLE_INFO", 'Here you can set basics for contact form.');
define("CONTACT_FORM_NAME", 'Your name');
define("CONTACT_FORM_NAME_REQ", 'Field name is mandatory!');
define("CONTACT_FORM_EMAIL", 'Contact email');
define("CONTACT_FORM_EMAIL_REQ", 'Email field is mandatory!');
define("CONTACT_FORM_SUBJECT", 'Subject');
define("CONTACT_FORM_SUBJECT_REQ", 'Field subject is mandatory');
define("CONTACT_FORM_ATTACHMENT", 'Attachment');
define("CONTACT_FORM_ATTACHMENT_INFO", 'Sender can attache the attachment to the contact message.');
define("CONTACT_FORM_TEXT", 'Your message');
define("CONTACT_FORM_TEXT_REQ", 'Message body is mandatory');
define("CONTACT_FORM_BUTTON_CONFIRM", 'Send');

define("CONTACT_FORM_SETTING_BACKEND_TITLE", 'Title of the contact form');
define("CONTACT_FORM_SETTING_BACKEND_CONTENT", 'Content of the contact form');
define("CONTACT_FORM_SETTING_BACKGROUND_COLOR", 'Color of the contact form background');
define("CONTACT_FORM_SETTING_COLOR", 'Font colour in the contact form');
define("CONTACT_FORM_SETTING_RECIPIENT", 'An email recipient from contact form.');
define("CONTACT_FORM_SETTING_RECIPIENT_VALIDATION", 'An email address of an email recipient is mandatory field!');
define("CONTACT_FORM_SETTING_RECIPIENT_INFO", 'On this email address will be send a request from contact form.');
define("CONTACT_FORM_SETTING_ATTACHMENT", 'Enabled attachment');
define("CONTACT_FORM_SETTING_SAVE", 'Save');
define("CONTACT_FORM_SETTING_COMPLETE_SAVE", 'Setting of the contact form has been saved');
define("CONTACT_FORM_WAS_SENT", 'Your question has been successfully sent.');
define("CONTACT_FORM_SENT_FAILED", 'Mandatory fields have not been filled.');
define("CONTACT_FORM_UNSUPPORTED_FILE_FORMAT", 'You are trying to send unsupported attachment!');
define("CONTACT_FORM_EMAIL_MY_SUBJECT", 'Inquiry from web contact form');

// footer
define("FOOTER_CONTENT", 'Footer content');
define("FOOTER_BUTTON_SAVE", "Save");
define("FOOTER_TITLE", "Footer settings");
define("FOOTER_INFO", "Footer is piece of content which will be showed in the bottom of each page.");
define("FOOTER_CONTACT", "Show contact form in page footer");
define("FOOTER_CONTENT_TEXT", 'Footer content you can format with text editor. If you want to show picture in the footer is necessary to load the picture first
								and then paste the URL link of picture to the editor with picture wizard.');
define("FOOTER_CONTENT_PICS", 'Here load a picture which you want to have in the footer content.');
define("FOOTER_BACKGROUND_COLOR", 'Footer background colour');
define("FOOTER_SETTING_COLOR", 'Footer text colour');
define("FOOTER_SETTING_SAVED", 'Footer settings have been saved');
define("FOOTER_PIC_FORMAT", "Supported image formats are BMP, JPG, PNG! Please load just these formats.");
define("FOOTER_PIC_DELETE", 'Image deletions');
define("FOOTER_PIC_DELETED", 'Image has been successfully removed');
define("FOOTER_CONFIRM_DELETE_TITLE", 'Picture deletions');
define("FOOTER_CONFIRM_DELETE", 'Are you really want to delete this image?');
define("FOOTER_CONFIRM_DELETE_CANCEL", 'Cancel');
define("FOOTER_CONFIRM_DELETE_OK", 'Delete');
define("FOOTER_SHOW_FOOTER", 'Turn on the footer');
define("FOOTER_WIDTH", 'Width of the footer');
define("FOOTER_WIDTH_INFO", 'Footer width setting toward the whole width of the browser window');
define("FOOTER_PIC_CONTENT", 'Images which is possible to paste into footer content');


// block
define("BLOCK_SETTING_TITLE", 'Contents block');
define("BLOCK_SETTING_INFO", 'Content block are pieces which is possible to assembly to whole content of the menu link. One page (menu link) can
							creates just one or more page blocks. In this section you can sets this block (page contents).');
define("BLOCK_SETTING_CONTENT", 'Content');
define("BLOCK_SETTING_BG_COLOR", 'Background colour');
define("BLOCK_SETTING_COLOR", 'Font colour');
define("BLOCK_SETTING_EDIT_ITEM", 'Block editions');
define("BLOCK_SETTING_DELETE_ITEM", 'Block deletions');
define("BLOCK_SETTING_PICS", 'Available images');
define("BLOCK_SETTING_WIDTH", 'Width');
define("BLOCK_SETTING_PICS_INFO", 'Available images is possible to pasto to the page block. Just copy the url of the image and paste it in the text editor image wizard
								then select resolution of this image and save the block content. <br />
 								<b>NOTE: </b> If you want to paste new image is necessary to save the image first and then you wil be able to paste them in to the content.');
define("BLOCK_SETTING_ITEM_EDIT", 'Block settings');
define("BLOCK_SETTING_ITEM_EDIT_INFO", 'Here you can set the whole block setting including its language(s).');
define("BLOCK_SETTING_ITEM_CONTENT_LABEL", 'Block content');
define("BLOCK_SETTING_ITEM_CONTENT_CONFIRM", 'Save the block');
define("BLOCK_SETTING_ITEM_CONTENT_COLOR", 'Font colour');
define("BLOCK_SETTING_ITEM_CONTENT_BG_COLOR", 'Background block colour');
define("BLOCK_SETTING_ITEM_WIDTH_INFO", 'Block width setting toward width of the browser width.');
define("BLOCK_SETTING_PIC_WILL_DELETE", 'Image deletions');
define("BLOCK_SETTING_PIC_DELETED", 'Image has been successfully deleted');
define("BLOCK_SETTING_PIC_DELETE_TITLE", 'Image deletions');
define("BLOCK_SETTING_PIC_DELETE", 'Are you really want to delete this picture?');
define("BLOCK_SETTING_PIC_DELETE_CANCEL", 'Cancel');
define("BLOCK_SETTING_PIC_DELETE_OK", 'Delete');
define("BLOCK_SETTINGS_CONFIRM_MODAL_DELETE_TITLE", 'Block deletions');
define("BLOCK_SETTINGS_CONFIRM_MODAL_DELETE_MSG", 'Are you really wnat to delete block of content?');
define("BLOCK_SETTINGS_CONFIRM_MODAL_OK", 'Delete');
define("BLOCK_SETTINGS_CONFIRM_MODAL_CANCEL", 'Cancel');
define("BLOCK_SETTINGS_ITEM_DELETED", 'Item has been successfully deleted.');
define("BLOCK_SETTINGS_ITEM_DEFAULT_BLOCK", 'This cannot be deleted bacause it is default home block!');
define("BLOCK_SETTINGS_ITEM_DELETED_FAILED", 'During the deletions error occurred, please try it again later!');
define("BLOCK_SETTINGS_ITEM_SAVED_FAILED", 'During saving the item, error occurred, please try again later!');

// block and content
define("BLOCK_CONTENT_SETTINGS", 'Web page content');
define("BLOCK_CONTENT_SETTINGS_INFO", 'In this section you are able to assembly content. Links from menu are putting together with block. Each item
										(subitem) from menu should have at least one block of content. As pagew title is used the one who is added in menu item.');
define("BLOCK_CONTENT_SETTINGS_BLOCKS_IN_MENU", 'Content of the page link');
define("BLOCK_CONTENT_SETTINGS_BLOCKS_IN_CONTENT", 'Blocks added to link');
define("BLOCK_CONTENT_SETTINGS_BLOCKS_IN_CONTENT_INFO", 'Block in the links create a content of the page (link). You are able to add more then one blocks to the link.
														The ordering means the order the blocks in the page. One link should have at least one block.');
define("BLOCK_CONTENT_SETTINGS_AVAILABLE_BLOCKS", 'Available blocks');
define("BLOCK_CONTENT_SETTINGS_AVAILABLE_BLOCKS_INFO", 'Blocks which is possible to add to the link (page).');
define("BLOCK_CONTENT_SETTINGS_CONTACT_FORM_AS_BLOCK", 'Contact form');
define("BLOCK_CONTENT_SETTINGS_ADD_TITLE", 'Add block to the link');
define("BLOCK_CONTENT_SETTINGS_REMOVE_TITLE", 'Remove block from the link');
define("BLOCK_CONTENT_SETTINGS_MOVE_BLOCK_UP", 'Move the block up');
define("BLOCK_CONTENT_SETTINGS_MOVE_BLOCK_DOWN", 'Move the block down');
define("BLOCK_CONTENT_SETTINGS_NO_BLOCKS", '-- empty --');

// language
define("LANG_SETTINGS", 'Language mutations');
define("LANG_SETTINGS_GLOBAL", 'Language bar setting');
define("LANG_BG_COLOR", 'Background colour of the menu bar');
define("LANG_BG_COLOR_INFO", 'Please choose desired color for the background.');
define("LANG_FONT_COLOR", 'Colour of the link in language strap');
define("LANG_FONT_COLOR_INFO", 'Please choose colour for the text in language bar');
define("LANG_ITEM_FLAG", 'Flag of the language');
define("LANG_ITEM_DESC", 'Language title');
define("LANG_ITEM_SHORT", 'Language code');
define("LANG_TITLE_INFO", "Here you can configure language mutation and its display.");
define("LANG_WIDTH", 'Width of the language bar');
define("LANG_WIDTH_INFO", 'Percent width of the browser window');
define("LANG_ALREADY_SAVED_LANGS", 'Available language mutations');
define("LANG_ALREADY_NEW_LANG", 'New language mutation saving');
define("LANG_CONFIRM", 'Save language setting');
define("LANG_TABLE_SHORTCUT", 'Language');
define("LANG_TABLE_FLAG", 'Flag icon');
define("LANG_TABLE_DELETE", 'Remove the language mutation');
define("LANG_CONFIRM_MODAL_DELETE_TITLE", 'Deletion of the language mutation');
define("LANG_CONFIRM_MODAL_DELETE_MSG", 'Are you really want to delete language mutation?');
define("LANG_CONFIRM_MODAL_OK", 'Delete');
define("LANG_CONFIRM_MODAL_CANCEL", 'Cancel');

// header
define("HEADER_SETTING_SAVED", 'Header settings have been successfully saved');
define("HEADER_SETTING_COLOR", 'Font colour');
define("HEADER_ARTICLES_COLOR", 'Articles font colour');
define("HEADER_ARTICLES_BG_COLOR", 'Articles background colour');
define("HEADER_ARTICLES_COUNT", 'Articles count');
define("HEADER_ARTICLES_COUNT_TEXT", 'Type number of last articles which will be visible here. If you want to disable this banner, type 0 (zero).');
define("HEADER_ARTICLES_TIMING", 'Articles change timing');
define("HEADER_ARTICLES_HEADER", 'Articles header');
define("HEADER_HEIGHT", 'Height of the header');
define("HEADER_HEIGHT_INFO", 'Fill height of the header i pixels');
define("HEADER_BACKGROUND_COLOR", 'Header background colour');
define("HEADER_TITLE", 'Page header setting');
define("HEADER_CONTENT", 'Content of the page header');
define("HEADER_INFO", 'Here you can set static header properties which will be showed in each page');
define("HEADER_SHOW_HEADER", 'Show header');
define("HEADER_WIDTH", 'Width of the page header');
define("HEADER_WIDTH_INFO", 'Width of the page header towards web browser');
define("HEADER_CONTENT_TEXT", 'Content of the page header');
define("HEADER_CONTENT_PICS", 'Images of the page header');
define("HEADER_PIC_CONTENT", 'Available images for page header');
define("HEADER_CONFIRM_DELETE_TITLE", 'Page header image deletions');
define("HEADER_CONFIRM_DELETE", 'Are you really want to delete image of the web header?');
define("HEADER_PIC_DELETE", 'Page header image deletions');
define("HEADER_CONFIRM_DELETE_CANCEL", 'Cancel');
define("HEADER_CONFIRM_DELETE_OK", 'Delete');
define("HEADER_BUTTON_SAVE", 'Save header setting');

// common
define("UNSUPPORTED_UPLOAD_FORMAT", " You are trying to upload unsupported format. Supported formats are %s.");
define("RECAPTCHA_NOT_A_BOT", "Not a bot?");
define("RECAPTCHA_CHECK_IT", "Please, verify that you are a human!");

define("COOKIE_SUBMIT_TEXT", "This website uses cookies to provide services, personalize ads and analyze traffic. By using this website, you agree to this.");
define("COOKIE_SUBMIT_OK", "OK");
define("COOKIE_SUBMIT_MORE_INFO", "More information");