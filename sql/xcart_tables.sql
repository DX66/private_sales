







CREATE TABLE xcart_address_book (
  id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  default_s char(1) NOT NULL default 'N',
  default_b char(1) NOT NULL default 'N',
  title varchar(32) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  lastname varchar(128) NOT NULL default '',
  address varchar(255) NOT NULL default '',
  city varchar(64) NOT NULL default '',
  county varchar(32) NOT NULL default '',
  state varchar(32) NOT NULL default '',
  country char(2) NOT NULL default '',
  zipcode varchar(32) NOT NULL default '',
  zip4 varchar(4) NOT NULL default '',
  phone varchar(32) NOT NULL default '',
  fax varchar(32) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY userid (userid),
  KEY default_s (userid,default_s),
  KEY default_b (userid,default_b)
) TYPE=MyISAM;





CREATE TABLE xcart_amazon_data (
  ref varchar(255) NOT NULL default '',
  cart mediumtext NOT NULL,
  PRIMARY KEY  (ref)
) TYPE=MyISAM;





CREATE TABLE xcart_amazon_orders (
  orderid int(11) NOT NULL default '0',
  amazon_oid varchar(255) NOT NULL default '',
  total decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (orderid),
  KEY amazon_oid (amazon_oid)
) TYPE=MyISAM;





CREATE TABLE xcart_benchmark_pages (
  pageid int(11) NOT NULL auto_increment,
  script varchar(64) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  method char(1) NOT NULL default 'G',
  PRIMARY KEY  (pageid),
  UNIQUE KEY sdm (script,data,method)
) TYPE=MyISAM;





CREATE TABLE xcart_bonus_memberships (
  bonusid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (bonusid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_categories (
  categoryid int(11) NOT NULL auto_increment,
  parentid int(11) NOT NULL default '0',
  category varchar(255) NOT NULL default '',
  description text NOT NULL,
  meta_description text NOT NULL,
  avail char(1) NOT NULL default 'Y',
  views_stats int(11) NOT NULL default '0',
  order_by int(11) NOT NULL default '0',
  threshold_bestsellers int(11) NOT NULL default '1',
  product_count int(11) NOT NULL default '0',
  top_product_count int(11) NOT NULL default '0',
  meta_keywords text NOT NULL,
  override_child_meta char(1) NOT NULL default 'Y',
  title_tag text NOT NULL,
  lpos int(11) NOT NULL default '0',
  rpos int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid),
  UNIQUE KEY ia (categoryid,avail),
  KEY avail (avail),
  KEY order_by (order_by,category),
  KEY lpos (lpos),
  KEY rpos (rpos),
  KEY parentid (parentid),
  KEY poc (parentid,order_by,category),
  KEY pa (lpos,avail)
) TYPE=MyISAM;





CREATE TABLE xcart_categories_lng (
  code char(2) NOT NULL default '',
  categoryid int(11) NOT NULL default '0',
  category varchar(255) NOT NULL default '',
  description text NOT NULL,
  PRIMARY KEY  (code,categoryid)
) TYPE=MyISAM;





CREATE TABLE xcart_categories_subcount (
  categoryid int(11) NOT NULL default '0',
  subcategory_count int(11) NOT NULL default '0',
  product_count int(11) NOT NULL default '0',
  top_product_count int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_category_bookmarks (
  categoryid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  UNIQUE KEY categoryid (categoryid,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_category_memberships (
  categoryid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_cc_gestpay_data (
  value char(32) NOT NULL default '',
  type char(1) NOT NULL default 'C',
  PRIMARY KEY  (value,type)
) TYPE=MyISAM;





CREATE TABLE xcart_cc_pp3_data (
  ref varchar(255) NOT NULL default '',
  sessionid varchar(255) NOT NULL default '',
  param1 varchar(255) NOT NULL default '',
  param2 varchar(255) NOT NULL default '',
  param3 varchar(255) NOT NULL default '',
  param4 varchar(255) NOT NULL default '',
  param5 varchar(255) NOT NULL default '',
  trstat varchar(255) NOT NULL default '',
  is_callback char(1) NOT NULL default '',
  UNIQUE KEY refk (ref)
) TYPE=MyISAM;





CREATE TABLE xcart_ccprocessors (
  module_name varchar(255) NOT NULL default '',
  type char(1) NOT NULL default '',
  processor varchar(255) NOT NULL default '',
  template varchar(255) NOT NULL default '',
  param01 varchar(255) NOT NULL default '',
  param02 varchar(255) NOT NULL default '',
  param03 varchar(255) NOT NULL default '',
  param04 varchar(255) NOT NULL default '',
  param05 varchar(255) NOT NULL default '',
  param06 varchar(255) NOT NULL default '',
  param07 varchar(255) NOT NULL default '',
  param08 varchar(255) NOT NULL default '',
  param09 varchar(255) NOT NULL default '',
  disable_ccinfo char(1) NOT NULL default 'N',
  background char(1) NOT NULL default 'N',
  testmode char(1) NOT NULL default 'N',
  is_check char(1) NOT NULL default '',
  is_refund char(1) NOT NULL default '',
  c_template varchar(255) NOT NULL default '',
  paymentid int(11) NOT NULL default '0',
  cmpi char(1) NOT NULL default '',
  use_preauth char(1) NOT NULL default '',
  preauth_expire int(11) NOT NULL default '0',
  has_preauth char(1) NOT NULL default '',
  capture_min_limit varchar(32) NOT NULL default '0%',
  capture_max_limit varchar(32) NOT NULL default '0%',
  PRIMARY KEY  (module_name),
  UNIQUE KEY pphm (paymentid,preauth_expire,has_preauth,module_name),
  KEY paymentid (paymentid),
  KEY processor (processor)
) TYPE=MyISAM;





CREATE TABLE xcart_change_password (
  userid int(11) NOT NULL default '0',
  password_reset_key varchar(32) NOT NULL default '',
  password_reset_key_date int(11) NOT NULL default '0',
  PRIMARY KEY  (userid),
  UNIQUE KEY password_reset_key (password_reset_key)
) TYPE=MyISAM;





CREATE TABLE xcart_class_lng (
  code char(2) NOT NULL default 'en',
  classid int(11) NOT NULL default '0',
  class varchar(128) NOT NULL default '',
  classtext varchar(255) NOT NULL default '',
  PRIMARY KEY  (classid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_class_options (
  optionid int(11) NOT NULL auto_increment,
  classid int(11) NOT NULL default '0',
  option_name varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  price_modifier decimal(12,2) NOT NULL default '0.00',
  modifier_type char(1) NOT NULL default '$',
  PRIMARY KEY  (optionid),
  KEY orderby (orderby,avail),
  KEY ia (classid,avail)
) TYPE=MyISAM;





CREATE TABLE xcart_classes (
  classid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  class varchar(128) NOT NULL default '',
  classtext varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  is_modifier char(1) NOT NULL default 'Y',
  PRIMARY KEY  (classid),
  KEY orderby (orderby,avail),
  KEY productid (productid),
  KEY is_modifier (is_modifier),
  KEY class (class)
) TYPE=MyISAM;





CREATE TABLE xcart_clean_urls (
  clean_url varchar(250) NOT NULL default '',
  resource_type char(1) NOT NULL default '',
  resource_id int(11) NOT NULL default '0',
  mtime int(11) NOT NULL default '0',
  PRIMARY KEY  (clean_url),
  KEY rr (resource_type,resource_id)
) TYPE=MyISAM;





CREATE TABLE xcart_clean_urls_history (
  id int(11) NOT NULL auto_increment,
  resource_type char(1) NOT NULL default '',
  resource_id int(11) NOT NULL default '0',
  clean_url varchar(250) NOT NULL default '',
  mtime int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY rrc (resource_type,resource_id,clean_url)
) TYPE=MyISAM;





CREATE TABLE xcart_condition_memberships (
  conditionid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (conditionid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_config (
  name varchar(32) NOT NULL default '',
  comment varchar(255) NOT NULL default '',
  value text NOT NULL,
  category varchar(32) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  type enum('numeric','text','textarea','checkbox','password','separator','selector','multiselector','state','country') default 'text',
  defvalue text NOT NULL,
  variants text NOT NULL,
  validation varchar(255) NOT NULL default '',
  PRIMARY KEY  (name),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_contact_fields (
  fieldid int(11) NOT NULL auto_increment,
  field varchar(255) NOT NULL default '',
  type char(1) NOT NULL default 'T',
  variants text NOT NULL,
  def varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail varchar(4) NOT NULL default '',
  required varchar(4) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY avail (avail),
  KEY required (required)
) TYPE=MyISAM;





CREATE TABLE xcart_counties (
  countyid int(11) NOT NULL auto_increment,
  stateid int(11) NOT NULL default '0',
  county varchar(255) NOT NULL default '',
  PRIMARY KEY  (countyid),
  UNIQUE KEY countyname (stateid,county),
  KEY countyid (stateid,countyid)
) TYPE=MyISAM;





CREATE TABLE xcart_countries (
  code char(2) NOT NULL default '',
  code_A3 char(3) NOT NULL default '',
  code_N3 int(4) NOT NULL default '0',
  region char(2) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  display_states char(1) NOT NULL default 'Y',
  PRIMARY KEY  (code)
) TYPE=MyISAM;





CREATE TABLE xcart_country_currencies (
  code char(3) NOT NULL default '',
  country_code char(2) NOT NULL default '',
  PRIMARY KEY  (code,country_code)
) TYPE=MyISAM;





CREATE TABLE xcart_currencies (
  code char(3) NOT NULL default '',
  code_int int(3) NOT NULL default '0',
  name varchar(128) NOT NULL default '',
  symbol varchar(16) NOT NULL default '',
  UNIQUE KEY code (code),
  KEY code_int (code_int)
) TYPE=MyISAM;





CREATE TABLE xcart_customer_bonuses (
  userid int(11) NOT NULL default '0',
  points int(11) NOT NULL default '0',
  memberships text NOT NULL,
  PRIMARY KEY  (userid)
) TYPE=MyISAM;





CREATE TABLE xcart_customers (
  id int(11) NOT NULL auto_increment,
  login varchar(128) NOT NULL default '',
  username varchar(128) NOT NULL default '',
  usertype char(1) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  invalid_login_attempts int(11) NOT NULL default '0',
  title varchar(32) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  lastname varchar(128) NOT NULL default '',
  company varchar(255) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  url varchar(128) NOT NULL default '',
  card_name varchar(255) NOT NULL default '',
  card_type varchar(16) NOT NULL default '',
  card_number varchar(128) NOT NULL default '',
  card_expire varchar(4) NOT NULL default '',
  card_cvv2 varchar(64) NOT NULL default '',
  card_valid_from varchar(4) NOT NULL default '',
  card_issue_no varchar(4) NOT NULL default '',
  last_login int(11) NOT NULL default '0',
  first_login int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'Y',
  activation_key varchar(32) NOT NULL default '',
  autolock char(1) NOT NULL default 'N',
  suspend_date int(11) NOT NULL default '0',
  referer varchar(255) NOT NULL default '',
  ssn varchar(32) NOT NULL default '',
  language char(2) NOT NULL default 'en',
  cart mediumtext NOT NULL,
  change_password char(1) NOT NULL default 'N',
  change_password_date int(11) NOT NULL default '0',
  parent int(11) NOT NULL default '0',
  pending_plan_id int(11) NOT NULL default '0',
  activity char(1) NOT NULL default 'Y',
  membershipid int(11) NOT NULL default '0',
  pending_membershipid int(11) NOT NULL default '0',
  tax_number varchar(50) NOT NULL default '',
  tax_exempt char(1) NOT NULL default 'N',
  trusted_provider char(1) NOT NULL default 'Y',
  PRIMARY KEY  (id),
  KEY login (login),
  KEY usertype (usertype),
  KEY last_login (last_login),
  KEY first_login (first_login),
  KEY status (status),
  KEY activation_key (activation_key),
  KEY membershipid (membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_delayed_queries (
  id int(11) NOT NULL auto_increment,
  query_type varchar(255) NOT NULL default '',
  query text NOT NULL,
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY qd (query_type,date),
  KEY date_key (date)
) TYPE=MyISAM;





CREATE TABLE xcart_delivery (
  shippingid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (shippingid,productid),
  KEY productid_index (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_coupons (
  coupon char(16) NOT NULL default '',
  discount decimal(12,2) NOT NULL default '0.00',
  coupon_type char(12) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  categoryid int(11) NOT NULL default '0',
  minimum decimal(12,2) NOT NULL default '0.00',
  times int(11) NOT NULL default '0',
  per_user char(1) NOT NULL default 'N',
  times_used int(11) NOT NULL default '0',
  expire int(11) NOT NULL default '0',
  status char(1) NOT NULL default '',
  provider int(11) NOT NULL default '0',
  recursive char(1) NOT NULL default 'N',
  apply_category_once char(1) NOT NULL default 'N',
  apply_product_once char(1) NOT NULL default 'N',
  PRIMARY KEY  (coupon),
  KEY provider (provider),
  KEY status (status)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_coupons_login (
  coupon varchar(16) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  times_used int(11) NOT NULL default '0',
  PRIMARY KEY  (coupon,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_memberships (
  discountid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (discountid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_discounts (
  discountid int(11) NOT NULL auto_increment,
  minprice decimal(12,2) NOT NULL default '0.00',
  discount decimal(12,2) NOT NULL default '0.00',
  discount_type char(32) NOT NULL default 'absolute',
  provider int(11) NOT NULL default '0',
  PRIMARY KEY  (discountid),
  KEY provider (provider),
  KEY minprice (minprice)
) TYPE=MyISAM;





CREATE TABLE xcart_download_keys (
  download_key char(100) NOT NULL default '',
  expires int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  itemid int(11) NOT NULL default '0',
  PRIMARY KEY  (download_key),
  UNIQUE KEY itemid (itemid),
  KEY productid (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_export_ranges (
  sec varchar(64) NOT NULL default '',
  id varchar(64) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  PRIMARY KEY  (sec,id,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_field_values (
  productid int(11) NOT NULL default '0',
  fieldid int(11) NOT NULL default '0',
  value char(255) NOT NULL default '',
  PRIMARY KEY  (productid,fieldid),
  FULLTEXT KEY value (value)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_fields (
  fieldid int(11) NOT NULL auto_increment,
  provider int(11) NOT NULL default '0',
  field char(255) NOT NULL default '',
  value char(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  service_name char(32) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY provider (provider),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_fields_lng (
  fieldid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'en',
  field char(255) NOT NULL default '',
  UNIQUE KEY fc (fieldid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_classes (
  fclassid int(11) NOT NULL auto_increment,
  class varchar(128) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  provider int(11) NOT NULL default '0',
  PRIMARY KEY  (fclassid),
  UNIQUE KEY fao (fclassid,avail,orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_classes_lng (
  fclassid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'en',
  class varchar(128) NOT NULL default '',
  PRIMARY KEY  (fclassid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_options (
  foptionid int(11) NOT NULL auto_increment,
  fclassid int(11) NOT NULL default '0',
  option_name varchar(128) NOT NULL default '',
  option_hint varchar(128) NOT NULL default '',
  option_type char(1) NOT NULL default '',
  format varchar(32) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  show_in_search char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (foptionid),
  KEY cao (fclassid,avail,orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_options_lng (
  foptionid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'en',
  option_name varchar(128) NOT NULL default '',
  option_hint varchar(128) NOT NULL default '',
  PRIMARY KEY  (foptionid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_variants (
  fvariantid int(11) NOT NULL auto_increment,
  foptionid int(11) NOT NULL default '0',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (fvariantid),
  KEY vo (fvariantid,foptionid)
) TYPE=MyISAM;





CREATE TABLE xcart_feature_variants_lng (
  fvariantid int(11) NOT NULL default '0',
  variant_name varchar(128) NOT NULL default '',
  code char(2) NOT NULL default 'en',
  PRIMARY KEY  (fvariantid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_featured_products (
  productid int(11) NOT NULL default '0',
  categoryid int(11) NOT NULL default '0',
  product_order int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  PRIMARY KEY  (productid,categoryid),
  KEY product_order (product_order),
  KEY avail (avail),
  KEY pacpo (productid,avail,categoryid,product_order)
) TYPE=MyISAM;





CREATE TABLE xcart_form_ids (
  sessid char(32) NOT NULL default '',
  formid char(32) NOT NULL default '',
  expire int(11) NOT NULL default '0',
  PRIMARY KEY  (sessid,formid),
  KEY expire (expire),
  KEY se (sessid,expire)
) TYPE=MyISAM;





CREATE TABLE xcart_gcheckout_orders (
  orderid int(11) NOT NULL default '0',
  goid varchar(255) NOT NULL default '',
  total decimal(12,2) NOT NULL default '0.00',
  refunded_amount decimal(12,2) NOT NULL default '0.00',
  fulfillment_state varchar(255) NOT NULL default '',
  financial_state varchar(255) NOT NULL default '',
  state_log text NOT NULL,
  archived char(1) NOT NULL default 'N',
  PRIMARY KEY  (orderid),
  KEY goid (goid)
) TYPE=MyISAM;





CREATE TABLE xcart_gcheckout_restrictions (
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_ge_products (
  sessid varchar(40) NOT NULL default '',
  geid varchar(32) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  UNIQUE KEY sgp (sessid,geid,productid),
  KEY geid (geid)
) TYPE=MyISAM;





CREATE TABLE xcart_giftcerts (
  gcid varchar(16) NOT NULL default '',
  orderid int(11) NOT NULL default '0',
  purchaser varchar(64) NOT NULL default '',
  recipient varchar(64) NOT NULL default '',
  send_via char(1) NOT NULL default 'E',
  recipient_email varchar(64) NOT NULL default '',
  recipient_firstname varchar(128) NOT NULL default '',
  recipient_lastname varchar(128) NOT NULL default '',
  recipient_address varchar(128) NOT NULL default '',
  recipient_city varchar(64) NOT NULL default '',
  recipient_state varchar(32) NOT NULL default '',
  recipient_zipcode varchar(32) NOT NULL default '',
  recipient_zip4 varchar(4) NOT NULL default '',
  recipient_country char(2) NOT NULL default '',
  recipient_phone varchar(32) NOT NULL default '',
  message text NOT NULL,
  amount decimal(12,2) NOT NULL default '0.00',
  debit decimal(12,2) NOT NULL default '0.00',
  status char(1) NOT NULL default 'P',
  add_date int(11) NOT NULL default '0',
  block_date int(11) NOT NULL default '0',
  tpl_file varchar(255) NOT NULL default 'template_default.tpl',
  recipient_county varchar(32) NOT NULL default '',
  PRIMARY KEY  (gcid),
  KEY orderid (orderid),
  KEY status (status),
  KEY add_date (add_date)
) TYPE=MyISAM;





CREATE TABLE xcart_giftreg_events (
  event_id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  event_date int(11) NOT NULL default '0',
  description text NOT NULL,
  html_content text NOT NULL,
  sent_date int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'P',
  guestbook char(1) NOT NULL default 'N',
  PRIMARY KEY  (event_id),
  KEY userid (userid),
  KEY event_date (event_date)
) TYPE=MyISAM;





CREATE TABLE xcart_giftreg_guestbooks (
  message_id int(11) NOT NULL auto_increment,
  event_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  subject varchar(255) NOT NULL default '',
  message text NOT NULL,
  post_date int(11) NOT NULL default '0',
  moderator char(1) NOT NULL default 'N',
  PRIMARY KEY  (message_id),
  KEY event_id (event_id,post_date)
) TYPE=MyISAM;





CREATE TABLE xcart_giftreg_maillist (
  regid int(11) NOT NULL auto_increment,
  event_id int(11) NOT NULL default '0',
  recipient_name varchar(255) NOT NULL default '',
  recipient_email varchar(255) NOT NULL default '',
  status char(1) NOT NULL default 'P',
  status_date int(11) NOT NULL default '0',
  confirmation_code varchar(100) NOT NULL default '',
  PRIMARY KEY  (regid),
  UNIQUE KEY event_id (event_id,recipient_email),
  UNIQUE KEY confirmation_code (confirmation_code),
  KEY recipient_name (recipient_name)
) TYPE=MyISAM;





CREATE TABLE xcart_images_B (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_C (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_D (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  KEY image_path (image_path),
  KEY id (id)
) TYPE=MyISAM;





CREATE TABLE xcart_images_F (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_G (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_L (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_M (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_P (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_S (
  imageid int(11) NOT NULL auto_increment,
  id varchar(16) NOT NULL default '',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_T (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_W (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_Z (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  KEY image_path (image_path),
  KEY id (id)
) TYPE=MyISAM;





CREATE TABLE xcart_import_cache (
  data_type char(3) binary NOT NULL default '',
  id varchar(255) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  PRIMARY KEY  (data_type,id,userid),
  KEY du (data_type,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_iterations (
  sessid varchar(32) NOT NULL default '',
  code varchar(8) NOT NULL default '',
  id varchar(32) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  PRIMARY KEY  (sessid,code,id)
) TYPE=MyISAM;





CREATE TABLE xcart_language_codes (
  code char(2) NOT NULL default '',
  code3 char(3) NOT NULL default '',
  language varchar(128) NOT NULL default '',
  country_code char(2) NOT NULL default '',
  lngid int(11) NOT NULL auto_increment,
  charset varchar(32) NOT NULL default 'iso-8859-1',
  r2l char(1) NOT NULL default '',
  disabled char(1) NOT NULL default '',
  PRIMARY KEY  (lngid),
  UNIQUE KEY code3 (code3),
  UNIQUE KEY code2 (code),
  KEY country_code (country_code)
) TYPE=MyISAM;





CREATE TABLE xcart_languages (
  code char(2) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  value text NOT NULL,
  topic varchar(24) NOT NULL default '',
  PRIMARY KEY  (code,name),
  KEY code (code),
  KEY topic (topic)
) TYPE=MyISAM;





CREATE TABLE xcart_languages_alt (
  code char(2) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (code,name)
) TYPE=MyISAM;





CREATE TABLE xcart_login_history (
  userid int(11) NOT NULL default '0',
  date_time int(11) NOT NULL default '0',
  usertype char(1) NOT NULL default '',
  action varchar(32) NOT NULL default '',
  status varchar(32) NOT NULL default '',
  ip varchar(32) NOT NULL default '',
  PRIMARY KEY  (userid,date_time)
) TYPE=MyISAM;





CREATE TABLE xcart_manufacturers (
  manufacturerid int(11) NOT NULL auto_increment,
  manufacturer varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  descr text NOT NULL,
  orderby int(11) NOT NULL default '0',
  provider int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  meta_description text NOT NULL,
  meta_keywords text NOT NULL,
  title_tag text NOT NULL,
  PRIMARY KEY  (manufacturerid),
  KEY manufacturer (manufacturer),
  KEY orderby (orderby),
  KEY provider (provider),
  KEY avail (avail)
) TYPE=MyISAM;





CREATE TABLE xcart_manufacturers_lng (
  manufacturerid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'en',
  manufacturer varchar(255) NOT NULL default '',
  descr text NOT NULL,
  UNIQUE KEY mc (manufacturerid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_memberships (
  membershipid int(11) NOT NULL auto_increment,
  area char(1) NOT NULL default 'C',
  membership varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  flag char(2) NOT NULL default '',
  PRIMARY KEY  (membershipid),
  KEY area (area),
  KEY orderby (orderby),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_memberships_lng (
  membershipid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'en',
  membership varchar(255) NOT NULL default '',
  UNIQUE KEY mc (membershipid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_modules (
  moduleid int(11) NOT NULL auto_increment,
  module_name varchar(255) NOT NULL default '',
  module_descr varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  PRIMARY KEY  (moduleid),
  KEY module_name (module_name),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_newsletter (
  newsid int(11) NOT NULL auto_increment,
  subject varchar(128) NOT NULL default '',
  body text NOT NULL,
  send_date int(11) NOT NULL default '0',
  email1 varchar(128) NOT NULL default '',
  email2 varchar(128) NOT NULL default '',
  email3 varchar(128) NOT NULL default '',
  status char(1) NOT NULL default 'N',
  listid int(11) NOT NULL default '0',
  show_as_news char(1) NOT NULL default 'N',
  allow_html char(1) NOT NULL default 'N',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (newsid),
  KEY status (status),
  KEY send_date (send_date)
) TYPE=MyISAM;





CREATE TABLE xcart_newslist_subscription (
  listid int(11) NOT NULL default '0',
  email char(128) NOT NULL default '',
  to_be_sent char(1) NOT NULL default '',
  since_date int(11) NOT NULL default '0',
  PRIMARY KEY  (listid,email),
  KEY to_be_sent (to_be_sent)
) TYPE=MyISAM;





CREATE TABLE xcart_newslists (
  listid int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  descr text NOT NULL,
  show_as_news char(1) NOT NULL default 'N',
  avail char(1) NOT NULL default 'N',
  subscribe char(1) NOT NULL default 'N',
  lngcode char(2) NOT NULL default 'en',
  PRIMARY KEY  (listid)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_bonus_params (
  paramid int(11) NOT NULL auto_increment,
  bonusid int(11) NOT NULL default '0',
  setid int(11) NOT NULL default '0',
  param_type char(1) NOT NULL default '',
  param_id int(11) NOT NULL default '0',
  param_arg char(1) NOT NULL default '',
  param_qnty int(11) NOT NULL default '0',
  param_promo char(1) NOT NULL default 'N',
  PRIMARY KEY  (paramid),
  KEY bonus_id_type (bonusid,param_type,param_id,param_arg),
  KEY bonusid (bonusid),
  KEY setid (setid)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_bonuses (
  bonusid int(11) NOT NULL auto_increment,
  offerid int(11) NOT NULL default '0',
  bonus_type char(1) NOT NULL default '',
  amount_type char(1) NOT NULL default '',
  amount_min decimal(12,2) NOT NULL default '0.00',
  amount_max decimal(12,2) NOT NULL default '0.00',
  bonus_data text,
  provider int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'N',
  PRIMARY KEY  (bonusid),
  UNIQUE KEY b_type (offerid,bonus_type),
  KEY b_sprice (bonusid,avail,bonus_type,amount_type,amount_min,amount_max)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_condition_params (
  paramid int(11) NOT NULL auto_increment,
  conditionid int(11) NOT NULL default '0',
  setid int(11) NOT NULL default '0',
  param_type char(1) NOT NULL default '',
  param_id int(11) NOT NULL default '0',
  param_arg char(1) NOT NULL default '',
  param_qnty int(11) NOT NULL default '0',
  param_promo char(1) NOT NULL default 'N',
  PRIMARY KEY  (paramid),
  KEY args1 (param_type,param_id,param_arg),
  KEY conditionid (conditionid),
  KEY setid (setid)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_conditions (
  conditionid int(11) NOT NULL auto_increment,
  offerid int(11) NOT NULL default '0',
  condition_type char(1) NOT NULL default '',
  amount_type char(1) NOT NULL default '',
  amount_min decimal(12,2) NOT NULL default '0.00',
  amount_max decimal(12,2) NOT NULL default '0.00',
  condition_data text,
  provider int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'N',
  PRIMARY KEY  (conditionid),
  UNIQUE KEY c_type (offerid,condition_type)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_product_params (
  productid int(11) NOT NULL default '0',
  sp_discount_avail char(1) NOT NULL default 'N',
  bonus_points int(11) NOT NULL default '0',
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_offer_product_sets (
  setid int(11) NOT NULL auto_increment,
  offerid int(11) NOT NULL default '0',
  set_type char(1) NOT NULL default '',
  cb_id int(11) NOT NULL default '0',
  cb_type char(1) NOT NULL default '0',
  name varchar(32) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  appl_type char(1) NOT NULL default 'I',
  PRIMARY KEY  (setid),
  UNIQUE KEY set_item_id (setid,cb_id),
  KEY set_incl_type (cb_id,set_type,appl_type)
) TYPE=MyISAM;





CREATE TABLE xcart_offers (
  offerid int(11) NOT NULL auto_increment,
  offer_name varchar(255) NOT NULL default '',
  offer_start int(11) NOT NULL default '0',
  offer_end int(11) NOT NULL default '0',
  offer_avail char(1) NOT NULL default 'N',
  provider int(11) NOT NULL default '0',
  modified_time int(11) NOT NULL default '0',
  show_short_promo char(1) NOT NULL default 'Y',
  PRIMARY KEY  (offerid),
  KEY offer_avail (offer_avail,offer_start,offer_end,provider)
) TYPE=MyISAM;





CREATE TABLE xcart_offers_lng (
  offerid int(11) NOT NULL default '0',
  code char(2) NOT NULL default '',
  promo_short text,
  promo_long text,
  promo_checkout text,
  promo_items_amount text,
  PRIMARY KEY  (offerid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_old_passwords (
  id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  password varchar(64) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY lp (userid,password)
) TYPE=MyISAM;





CREATE TABLE xcart_order_details (
  orderid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  price decimal(12,2) NOT NULL default '0.00',
  amount int(11) NOT NULL default '0',
  provider int(11) NOT NULL default '0',
  product_options text NOT NULL,
  extra_data text NOT NULL,
  itemid int(11) NOT NULL auto_increment,
  productcode varchar(32) NOT NULL default '',
  product varchar(255) NOT NULL default '',
  PRIMARY KEY  (itemid),
  KEY orderid (orderid),
  KEY productid (productid),
  KEY provider (provider),
  KEY productcode (productcode)
) TYPE=MyISAM;





CREATE TABLE xcart_order_extras (
  orderid int(11) NOT NULL default '0',
  khash varchar(64) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (orderid,khash),
  UNIQUE KEY kvo (khash,value(32),orderid)
) TYPE=MyISAM;





CREATE TABLE xcart_order_status_history (
  recid int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  date_time int(11) NOT NULL default '0',
  details text NOT NULL,
  PRIMARY KEY  (recid),
  KEY orderid (orderid,date_time)
) TYPE=MyISAM;





CREATE TABLE xcart_orders (
  orderid int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  membership varchar(255) NOT NULL default '',
  total decimal(12,2) NOT NULL default '0.00',
  giftcert_discount decimal(12,2) NOT NULL default '0.00',
  giftcert_ids text NOT NULL,
  subtotal decimal(12,2) NOT NULL default '0.00',
  discount decimal(12,2) NOT NULL default '0.00',
  coupon varchar(32) NOT NULL default '',
  coupon_discount decimal(12,2) NOT NULL default '0.00',
  shippingid int(11) NOT NULL default '0',
  shipping varchar(255) NOT NULL default '',
  tracking varchar(64) NOT NULL default '',
  shipping_cost decimal(12,2) NOT NULL default '0.00',
  tax decimal(12,2) NOT NULL default '0.00',
  taxes_applied text NOT NULL,
  date int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'Q',
  payment_method varchar(128) NOT NULL default '',
  flag char(1) NOT NULL default 'N',
  notes text NOT NULL,
  details text NOT NULL,
  customer_notes text NOT NULL,
  customer varchar(32) NOT NULL default '',
  title varchar(32) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  lastname varchar(128) NOT NULL default '',
  company varchar(255) NOT NULL default '',
  b_title varchar(32) NOT NULL default '',
  b_firstname varchar(128) NOT NULL default '',
  b_lastname varchar(128) NOT NULL default '',
  b_address varchar(255) NOT NULL default '',
  b_city varchar(64) NOT NULL default '',
  b_county varchar(32) NOT NULL default '',
  b_state varchar(32) NOT NULL default '',
  b_country char(2) NOT NULL default '',
  b_zipcode varchar(32) NOT NULL default '',
  b_zip4 varchar(4) NOT NULL default '',
  b_phone varchar(32) NOT NULL default '',
  b_fax varchar(32) NOT NULL default '',
  s_title varchar(32) NOT NULL default '',
  s_firstname varchar(128) NOT NULL default '',
  s_lastname varchar(128) NOT NULL default '',
  s_address varchar(255) NOT NULL default '',
  s_city varchar(255) NOT NULL default '',
  s_county varchar(32) NOT NULL default '',
  s_state varchar(32) NOT NULL default '',
  s_country char(2) NOT NULL default '',
  s_zipcode varchar(32) NOT NULL default '',
  s_phone varchar(32) NOT NULL default '',
  s_fax varchar(32) NOT NULL default '',
  s_zip4 varchar(4) NOT NULL default '',
  url varchar(128) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  language char(2) NOT NULL default 'en',
  clickid int(11) NOT NULL default '0',
  extra mediumtext NOT NULL,
  membershipid int(11) NOT NULL default '0',
  paymentid int(11) NOT NULL default '0',
  payment_surcharge decimal(12,2) NOT NULL default '0.00',
  tax_number varchar(50) NOT NULL default '',
  tax_exempt char(1) NOT NULL default 'N',
  init_total decimal(12,2) NOT NULL default '0.00',
  access_key varchar(16) NOT NULL default '',
  PRIMARY KEY  (orderid),
  UNIQUE KEY odsp (orderid,date,status,paymentid),
  KEY order_date (date),
  KEY s_state (s_state),
  KEY b_state (b_state),
  KEY s_country (s_country),
  KEY b_country (b_country),
  KEY clickid (clickid),
  KEY userid (userid),
  KEY paymentid (paymentid),
  KEY shippingid (shippingid)
) TYPE=MyISAM;





CREATE TABLE xcart_packages_cache (
  md5_args varchar(32) NOT NULL default '',
  session_id varchar(32) NOT NULL default '',
  packages text NOT NULL,
  PRIMARY KEY  (md5_args,session_id)
) TYPE=MyISAM;





CREATE TABLE xcart_pages (
  pageid int(11) NOT NULL auto_increment,
  filename varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  level char(1) NOT NULL default 'E',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  language char(2) NOT NULL default '',
  show_in_menu char(1) NOT NULL default '',
  meta_description text NOT NULL,
  meta_keywords text NOT NULL,
  title_tag text NOT NULL,
  PRIMARY KEY  (pageid),
  UNIQUE KEY filename_pageid (filename,pageid),
  KEY orderby (level,orderby,title)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_adv_campaigns (
  campaignid int(11) NOT NULL auto_increment,
  campaign varchar(128) NOT NULL default '',
  per_visit decimal(12,2) NOT NULL default '0.00',
  per_period decimal(12,2) NOT NULL default '0.00',
  start_period int(11) NOT NULL default '0',
  end_period int(11) NOT NULL default '0',
  type char(1) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  PRIMARY KEY  (campaignid),
  KEY type (type)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_adv_clicks (
  campaignid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  PRIMARY KEY  (campaignid,add_date)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_adv_orders (
  campaignid int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  PRIMARY KEY  (campaignid,orderid)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_banners (
  bannerid int(11) NOT NULL auto_increment,
  banner varchar(128) NOT NULL default '',
  body mediumblob NOT NULL,
  avail char(1) NOT NULL default 'Y',
  is_image char(1) NOT NULL default 'Y',
  is_name char(1) NOT NULL default 'Y',
  is_descr char(1) NOT NULL default 'Y',
  is_add char(1) NOT NULL default 'Y',
  banner_type char(1) NOT NULL default 'T',
  open_blank char(1) NOT NULL default 'Y',
  legend text NOT NULL,
  alt text NOT NULL,
  direction char(1) NOT NULL default 'D',
  banner_x int(11) NOT NULL default '0',
  banner_y int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  PRIMARY KEY  (bannerid),
  KEY userid (userid)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_clicks (
  userid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  bannerid int(11) NOT NULL default '0',
  target char(1) NOT NULL default '',
  targetid int(11) NOT NULL default '0',
  referer varchar(255) NOT NULL default '',
  clickid int(11) NOT NULL auto_increment,
  PRIMARY KEY  (clickid),
  KEY userid (userid),
  KEY add_date (add_date)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_commissions (
  userid int(11) NOT NULL default '0',
  plan_id int(11) NOT NULL default '0',
  PRIMARY KEY  (userid),
  KEY plan_id (plan_id)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_payment (
  payment_id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  commissions decimal(12,2) NOT NULL default '0.00',
  paid char(1) NOT NULL default 'N',
  add_date int(11) NOT NULL default '0',
  affiliate int(11) NOT NULL default '0',
  PRIMARY KEY  (payment_id),
  KEY userid (userid),
  KEY orderid (orderid),
  KEY affiliate (affiliate)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_plans (
  plan_id int(11) NOT NULL auto_increment,
  plan_title varchar(64) NOT NULL default '',
  status char(1) NOT NULL default 'A',
  min_paid decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (plan_id),
  KEY status (status)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_plans_commissions (
  plan_id int(11) NOT NULL default '0',
  commission decimal(12,2) NOT NULL default '0.00',
  commission_type enum('$','%') NOT NULL default '%',
  item_id int(11) NOT NULL default '0',
  item_type char(1) NOT NULL default 'A',
  PRIMARY KEY  (plan_id,item_id,item_type)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_product_commissions (
  itemid int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  product_commission decimal(12,2) NOT NULL default '0.00',
  userid int(11) NOT NULL default '0',
  PRIMARY KEY  (itemid,orderid,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_tier_commissions (
  plan_id int(11) NOT NULL default '0',
  level int(2) NOT NULL default '0',
  commission decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (plan_id,level)
) TYPE=MyISAM;





CREATE TABLE xcart_partner_views (
  userid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  bannerid int(11) NOT NULL default '0',
  target char(1) NOT NULL default '',
  targetid int(11) NOT NULL default '0',
  KEY userid (userid)
) TYPE=MyISAM;





CREATE TABLE xcart_payment_methods (
  paymentid int(11) NOT NULL auto_increment,
  payment_method varchar(128) NOT NULL default '',
  payment_details varchar(255) NOT NULL default '',
  payment_template varchar(128) NOT NULL default '',
  payment_script varchar(128) NOT NULL default '',
  protocol varchar(6) NOT NULL default 'http',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  is_cod char(1) NOT NULL default '',
  af_check char(1) NOT NULL default 'Y',
  processor_file varchar(255) NOT NULL default '',
  surcharge decimal(12,2) NOT NULL default '0.00',
  surcharge_type char(1) NOT NULL default '$',
  PRIMARY KEY  (paymentid),
  KEY orderby (orderby),
  KEY protocol (protocol)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_class_requirements (
  classid int(11) NOT NULL default '0',
  ptypeid int(11) NOT NULL default '0',
  specid int(11) NOT NULL default '0',
  PRIMARY KEY  (classid,ptypeid,specid)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_class_specifications (
  classid int(11) NOT NULL default '0',
  specid int(11) NOT NULL default '0',
  PRIMARY KEY  (classid,specid)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_product_types (
  ptypeid int(11) NOT NULL auto_increment,
  provider int(11) NOT NULL default '0',
  ptype_name varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (ptypeid),
  UNIQUE KEY provider (provider,ptype_name)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_products_classes (
  classid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  ptypeid int(11) NOT NULL default '0',
  PRIMARY KEY  (classid),
  UNIQUE KEY product_type (productid,ptypeid)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_slot_markups (
  markupid int(11) NOT NULL auto_increment,
  slotid int(11) NOT NULL default '0',
  markup decimal(12,2) NOT NULL default '0.00',
  markup_type char(1) NOT NULL default '%',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (markupid),
  UNIQUE KEY slotid (slotid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_slot_rules (
  slotid int(11) NOT NULL default '0',
  ptypeid int(11) NOT NULL default '0',
  index_by_and int(11) NOT NULL default '0',
  KEY slotid (slotid),
  KEY ptypeid (ptypeid),
  KEY index_by_and (index_by_and)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_slots (
  slotid int(11) NOT NULL auto_increment,
  stepid int(11) NOT NULL default '0',
  slot_name varchar(255) NOT NULL default '',
  slot_descr text NOT NULL,
  status char(1) NOT NULL default 'O',
  multiple char(1) NOT NULL default '',
  amount_min int(11) NOT NULL default '1',
  amount_max int(11) NOT NULL default '1',
  default_amount int(11) NOT NULL default '1',
  default_productid int(11) NOT NULL default '0',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (slotid),
  KEY product (stepid,orderby,slotid)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_specifications (
  specid int(11) NOT NULL auto_increment,
  ptypeid int(11) NOT NULL default '0',
  spec_name varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (specid),
  UNIQUE KEY name (ptypeid,spec_name)
) TYPE=MyISAM;





CREATE TABLE xcart_pconf_wizards (
  stepid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  step_name varchar(255) NOT NULL default '',
  step_descr text NOT NULL,
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (stepid),
  KEY product (productid,orderby,stepid)
) TYPE=MyISAM;





CREATE TABLE xcart_pmethod_memberships (
  paymentid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (paymentid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_pricing (
  priceid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  quantity int(11) NOT NULL default '0',
  price decimal(12,2) NOT NULL default '0.00',
  variantid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (priceid),
  KEY productid (productid),
  KEY variantid (variantid),
  KEY pvq (productid,variantid,quantity),
  KEY pvqm (productid,variantid,quantity,membershipid),
  KEY pv (productid,variantid),
  KEY vq (variantid,quantity),
  KEY vqm (variantid,quantity,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_bookmarks (
  productid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  UNIQUE KEY productid (productid,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_features (
  productid int(11) NOT NULL default '0',
  fclassid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,fclassid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_foptions (
  foptionid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  value text NOT NULL,
  PRIMARY KEY  (foptionid,productid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_links (
  productid1 int(11) NOT NULL default '0',
  productid2 int(11) NOT NULL default '0',
  orderby int(11) NOT NULL default '0',
  KEY productid2 (productid2),
  KEY productid1 (productid1),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_product_memberships (
  productid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_ex (
  optionid int(11) NOT NULL default '0',
  exceptionid int(11) NOT NULL default '0',
  PRIMARY KEY  (optionid,exceptionid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_js (
  productid int(11) NOT NULL default '0',
  javascript_code text,
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_lng (
  code char(2) NOT NULL default 'en',
  optionid int(11) NOT NULL default '0',
  option_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (code,optionid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_reviews (
  review_id int(11) NOT NULL auto_increment,
  remote_ip varchar(15) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  message text NOT NULL,
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (review_id),
  KEY productid (productid),
  KEY remote_ip (remote_ip)
) TYPE=MyISAM;





CREATE TABLE xcart_product_rnd_keys (
  productid int(11) NOT NULL default '0',
  rnd_key int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,rnd_key)
) TYPE=MyISAM;





CREATE TABLE xcart_product_taxes (
  productid int(11) NOT NULL default '0',
  taxid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,taxid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_votes (
  vote_id int(11) NOT NULL auto_increment,
  remote_ip varchar(15) NOT NULL default '',
  vote_value int(1) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (vote_id),
  KEY remote_ip (remote_ip),
  KEY productid (productid),
  KEY rp (remote_ip,productid)
) TYPE=MyISAM;





CREATE TABLE xcart_products (
  productid int(11) NOT NULL auto_increment,
  productcode varchar(32) NOT NULL default '',
  product varchar(255) NOT NULL default '',
  provider int(11) NOT NULL default '0',
  distribution varchar(255) NOT NULL default '',
  weight decimal(12,2) NOT NULL default '0.00',
  list_price decimal(12,2) NOT NULL default '0.00',
  descr text NOT NULL,
  fulldescr text NOT NULL,
  avail int(11) NOT NULL default '0',
  rating int(11) NOT NULL default '0',
  forsale char(1) NOT NULL default 'Y',
  add_date int(11) NOT NULL default '0',
  views_stats int(11) NOT NULL default '0',
  sales_stats int(11) NOT NULL default '0',
  del_stats int(11) NOT NULL default '0',
  shipping_freight decimal(12,2) NOT NULL default '0.00',
  free_shipping char(1) NOT NULL default 'N',
  discount_avail char(1) NOT NULL default 'Y',
  min_amount int(11) NOT NULL default '1',
  length decimal(12,2) NOT NULL default '0.00',
  width decimal(12,2) NOT NULL default '0.00',
  height decimal(12,2) NOT NULL default '0.00',
  low_avail_limit int(11) NOT NULL default '10',
  free_tax char(1) NOT NULL default 'N',
  product_type char(1) NOT NULL default 'N',
  manufacturerid int(11) NOT NULL default '0',
  return_time int(11) NOT NULL default '0',
  keywords varchar(255) NOT NULL default '',
  meta_description text NOT NULL,
  meta_keywords text NOT NULL,
  small_item char(1) NOT NULL default 'N',
  separate_box char(1) NOT NULL default 'N',
  items_per_box int(11) NOT NULL default '1',
  title_tag text NOT NULL,
  PRIMARY KEY  (productid),
  UNIQUE KEY productcode (productcode,provider),
  KEY product (product),
  KEY rating (rating),
  KEY add_date (add_date),
  KEY provider (provider),
  KEY avail (avail),
  KEY best_sellers (sales_stats,views_stats),
  KEY categories (forsale),
  KEY fi (forsale,productid),
  KEY fia (forsale,productid,avail),
  KEY ppp (productcode,provider,productid),
  KEY manufacturerid (manufacturerid)
) TYPE=MyISAM;





CREATE TABLE xcart_products_categories (
  categoryid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  main char(1) NOT NULL default 'N',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,productid),
  UNIQUE KEY cpm (categoryid,productid,main),
  KEY productid (productid),
  KEY main (main),
  KEY orderby (categoryid,orderby),
  KEY pm (productid,main)
) TYPE=MyISAM;





CREATE TABLE xcart_products_lng (
  code char(2) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  product varchar(255) NOT NULL default '',
  descr text NOT NULL,
  fulldescr text NOT NULL,
  keywords varchar(255) NOT NULL default '',
  PRIMARY KEY  (code,productid)
) TYPE=MyISAM;





CREATE TABLE xcart_provider_commissions (
  orderid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  commission_date int(11) NOT NULL default '0',
  paid char(1) NOT NULL default '',
  note tinytext NOT NULL,
  add_date int(11) NOT NULL default '0',
  commissions decimal(12,2) NOT NULL default '0.00',
  paid_commissions decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (orderid),
  KEY userid (userid)
) TYPE=MyISAM;





CREATE TABLE xcart_provider_product_commissions (
  itemid int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  product_commission decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (itemid,orderid,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_quick_flags (
  productid int(11) NOT NULL default '0',
  is_variants char(1) NOT NULL default '',
  is_product_options char(1) NOT NULL default '',
  is_taxes char(1) NOT NULL default '',
  image_path_T varchar(255) default NULL,
  PRIMARY KEY  (productid),
  UNIQUE KEY pi (productid,image_path_T),
  KEY vpt (is_variants,is_product_options,is_taxes)
) TYPE=MyISAM;





CREATE TABLE xcart_quick_prices (
  productid int(11) NOT NULL default '0',
  priceid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  variantid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,membershipid),
  KEY pp (productid,priceid),
  KEY pmp (productid,membershipid,priceid)
) TYPE=MyISAM;





CREATE TABLE xcart_referers (
  referer char(255) NOT NULL default '',
  visits int(11) NOT NULL default '0',
  last_visited int(11) NOT NULL default '0',
  PRIMARY KEY  (referer)
) TYPE=MyISAM;





CREATE TABLE xcart_register_field_values (
  fieldid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  value text NOT NULL,
  PRIMARY KEY  (fieldid,userid)
) TYPE=MyISAM;





CREATE TABLE xcart_register_fields (
  fieldid int(11) NOT NULL auto_increment,
  field varchar(255) NOT NULL default '',
  type char(1) NOT NULL default 'T',
  variants text NOT NULL,
  def varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  section char(1) NOT NULL default 'A',
  avail varchar(5) NOT NULL default '',
  required varchar(5) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY orderby (orderby),
  KEY avail (avail),
  KEY required (required)
) TYPE=MyISAM;





CREATE TABLE xcart_returns (
  returnid int(11) NOT NULL auto_increment,
  itemid int(11) NOT NULL default '0',
  amount int(11) NOT NULL default '0',
  returned_amount int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'R',
  reason int(11) NOT NULL default '0',
  action int(11) NOT NULL default '0',
  comment text NOT NULL,
  date int(11) NOT NULL default '0',
  credit varchar(16) NOT NULL default '',
  creator char(1) NOT NULL default 'C',
  PRIMARY KEY  (returnid),
  KEY itemid (itemid)
) TYPE=MyISAM;





CREATE TABLE xcart_secure3d_data (
  tranid varchar(32) NOT NULL default '',
  date int(11) NOT NULL default '0',
  get_data mediumtext NOT NULL,
  sessid varchar(32) NOT NULL default '',
  session_data text NOT NULL,
  form_data text NOT NULL,
  form_url text NOT NULL,
  return_data mediumtext NOT NULL,
  processor varchar(255) NOT NULL default '',
  verify_funcname varchar(255) NOT NULL default '',
  validate_funcname varchar(255) NOT NULL default '',
  md varchar(255) NOT NULL default '',
  no_iframe char(1) NOT NULL default '',
  service_data text NOT NULL,
  PRIMARY KEY  (tranid),
  KEY sessid (sessid),
  KEY md (md)
) TYPE=MyISAM;





CREATE TABLE xcart_seller_addresses (
  userid int(11) NOT NULL default '0',
  address varchar(255) NOT NULL default '',
  city varchar(64) NOT NULL default '',
  state varchar(32) NOT NULL default '',
  country char(2) NOT NULL default '',
  zipcode varchar(32) NOT NULL default '',
  arb_id text NOT NULL,
  arb_password text NOT NULL,
  arb_account text NOT NULL,
  arb_shipping_key text NOT NULL,
  arb_shipping_key_intl text NOT NULL,
  PRIMARY KEY  (userid)
) TYPE=MyISAM;





CREATE TABLE xcart_session_history (
  ip varchar(15) NOT NULL default '',
  host varchar(255) NOT NULL default '',
  xid varchar(32) NOT NULL default '',
  dest_xid varchar(32) NOT NULL default '',
  PRIMARY KEY  (ip,host),
  KEY ihx (ip,host,xid)
) TYPE=MyISAM;





CREATE TABLE xcart_session_unknown_sid (
  sessid varchar(40) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  cnt int(11) NOT NULL default '0',
  PRIMARY KEY  (sessid,ip),
  KEY ip (ip)
) TYPE=MyISAM;





CREATE TABLE xcart_sessions_data (
  sessid varchar(40) NOT NULL default '',
  start int(11) NOT NULL default '0',
  expiry int(11) NOT NULL default '0',
  data mediumtext NOT NULL,
  PRIMARY KEY  (sessid),
  UNIQUE KEY expiry_sid (expiry,sessid)
) TYPE=MyISAM;





CREATE TABLE xcart_setup_images (
  itype char(1) NOT NULL default '',
  location char(2) NOT NULL default 'DB',
  save_url char(1) NOT NULL default '',
  size_limit int(11) NOT NULL default '0',
  md5_check varchar(32) NOT NULL default '',
  default_image varchar(255) NOT NULL default './default_image.gif',
  UNIQUE KEY itype (itype)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping (
  shippingid int(11) NOT NULL auto_increment,
  shipping varchar(255) NOT NULL default '',
  shipping_time varchar(128) NOT NULL default '',
  destination char(1) NOT NULL default 'I',
  code varchar(32) NOT NULL default '',
  subcode varchar(32) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  intershipper_code varchar(32) NOT NULL default '',
  weight_min decimal(12,2) NOT NULL default '0.00',
  weight_limit decimal(12,2) NOT NULL default '0.00',
  service_code int(11) NOT NULL default '0',
  is_cod char(1) NOT NULL default '',
  is_new char(1) NOT NULL default '',
  amazon_service varchar(32) NOT NULL default 'Standard',
  gc_shipping varchar(30) NOT NULL default '',
  PRIMARY KEY  (shippingid),
  KEY code (code),
  KEY orderby (orderby),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_cache (
  md5_request varchar(32) NOT NULL default '',
  session_id varchar(32) NOT NULL default '',
  response text NOT NULL,
  PRIMARY KEY  (md5_request,session_id)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_labels (
  labelid int(11) NOT NULL auto_increment,
  orderid int(11) NOT NULL default '0',
  mime_type varchar(80) NOT NULL default '',
  label mediumblob NOT NULL,
  error text NOT NULL,
  descr varchar(255) NOT NULL default '',
  packages_number int(11) NOT NULL default '0',
  is_first char(1) NOT NULL default '',
  PRIMARY KEY  (labelid),
  KEY orderid (orderid)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_options (
  carrier varchar(32) NOT NULL default '',
  param00 text NOT NULL,
  param01 varchar(128) NOT NULL default '',
  param02 varchar(128) NOT NULL default '',
  param03 varchar(128) NOT NULL default '',
  param04 varchar(128) NOT NULL default '',
  param05 varchar(128) NOT NULL default '',
  param06 varchar(128) NOT NULL default '',
  param07 varchar(128) NOT NULL default '',
  param08 varchar(128) NOT NULL default '',
  param09 varchar(128) NOT NULL default '',
  param10 varchar(128) NOT NULL default '',
  param11 varchar(128) NOT NULL default '',
  currency_rate decimal(12,2) NOT NULL default '1.00',
  PRIMARY KEY  (carrier)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_rates (
  rateid int(11) NOT NULL auto_increment,
  shippingid int(11) NOT NULL default '0',
  zoneid int(11) NOT NULL default '0',
  maxamount int(11) NOT NULL default '1000000',
  minweight decimal(12,2) NOT NULL default '0.00',
  maxweight decimal(12,2) NOT NULL default '1000000.00',
  mintotal decimal(12,2) NOT NULL default '0.00',
  maxtotal decimal(12,2) NOT NULL default '0.00',
  rate decimal(12,2) NOT NULL default '0.00',
  item_rate decimal(12,2) NOT NULL default '0.00',
  weight_rate decimal(12,2) NOT NULL default '0.00',
  rate_p decimal(12,2) NOT NULL default '0.00',
  provider int(11) NOT NULL default '0',
  type char(1) NOT NULL default 'D',
  apply_to char(6) NOT NULL default 'DST',
  PRIMARY KEY  (rateid),
  KEY provider (provider),
  KEY shippingid (shippingid),
  KEY maxamount (maxamount),
  KEY maxweight (maxweight),
  KEY zoneid (zoneid)
) TYPE=MyISAM;





CREATE TABLE xcart_sitemap_extra (
  id int(11) NOT NULL auto_increment,
  url varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;





CREATE TABLE xcart_split_checkout (
  orderids varchar(255) NOT NULL default '',
  data mediumtext NOT NULL,
  PRIMARY KEY  (orderids)
) TYPE=MyISAM;





CREATE TABLE xcart_states (
  stateid int(11) NOT NULL auto_increment,
  state varchar(64) NOT NULL default '',
  code varchar(32) NOT NULL default '',
  country_code char(2) NOT NULL default '',
  PRIMARY KEY  (stateid),
  UNIQUE KEY code (country_code,code),
  KEY state (state)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_adaptive (
  platform varchar(64) NOT NULL default '',
  browser varchar(10) NOT NULL default '',
  version varchar(16) NOT NULL default '',
  java char(1) NOT NULL default 'Y',
  js char(1) NOT NULL default 'Y',
  count int(11) NOT NULL default '0',
  cookie char(1) NOT NULL default '',
  screen_x int(11) NOT NULL default '0',
  screen_y int(11) NOT NULL default '0',
  last_date int(11) NOT NULL default '0',
  PRIMARY KEY  (platform,browser,java,js,version,cookie,screen_x,screen_y)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_cart_funnel (
  transactionid int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  start_page int(11) NOT NULL default '0',
  step1 int(11) NOT NULL default '0',
  step2 int(11) NOT NULL default '0',
  step3 int(11) NOT NULL default '0',
  final_page int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (transactionid),
  KEY start_page (start_page),
  KEY step1 (step1),
  KEY step2 (step2),
  KEY step3 (step3),
  KEY final_page (final_page),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_customers_products (
  productid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  counter int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,userid),
  KEY counter (counter)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages (
  pageid int(11) NOT NULL auto_increment,
  page varchar(255) NOT NULL default '',
  PRIMARY KEY  (pageid),
  KEY page (page)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages_paths (
  path varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  KEY counter (date),
  KEY path (path)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages_views (
  pageid int(255) NOT NULL default '0',
  time_avg int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  KEY pageid (pageid),
  KEY time_avg (time_avg),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_search (
  swordid int(11) NOT NULL auto_increment,
  search varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (swordid),
  KEY search (search),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_shop (
  id int(11) NOT NULL default '0',
  action char(1) NOT NULL default 'V',
  date int(11) NOT NULL default '0',
  multi int(11) NOT NULL default '1',
  KEY id (id),
  KEY date (date),
  KEY action (action)
) TYPE=MyISAM;





CREATE TABLE xcart_stop_list (
  octet1 int(3) NOT NULL default '0',
  octet2 int(3) NOT NULL default '0',
  octet3 int(3) NOT NULL default '0',
  octet4 int(3) NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  reason char(1) NOT NULL default 'M',
  date int(11) NOT NULL default '0',
  ipid int(11) NOT NULL auto_increment,
  ip_type char(1) NOT NULL default 'B',
  PRIMARY KEY  (ipid),
  UNIQUE KEY octet1 (octet1,octet2,octet3,octet4),
  KEY ip (ip)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_answers (
  answerid int(11) NOT NULL auto_increment,
  questionid int(11) NOT NULL default '0',
  textbox_type char(1) NOT NULL default 'N',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (answerid),
  KEY questionid (questionid)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_events (
  surveyid int(11) NOT NULL default '0',
  param char(1) NOT NULL default '',
  id varchar(255) NOT NULL default '',
  PRIMARY KEY  (surveyid,param,id)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_maillist (
  surveyid int(11) NOT NULL default '0',
  email varchar(255) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  access_key varchar(32) NOT NULL default '',
  sent_date int(11) NOT NULL default '0',
  complete_date int(11) NOT NULL default '0',
  delay_date int(11) NOT NULL default '0',
  as_result varchar(32) NOT NULL default '',
  UNIQUE KEY se (surveyid,email),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_questions (
  questionid int(11) NOT NULL auto_increment,
  surveyid int(11) NOT NULL default '0',
  answers_type char(1) NOT NULL default 'R',
  required char(1) NOT NULL default '',
  col int(3) NOT NULL default '0',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (questionid),
  KEY surveyid (surveyid)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_result_answers (
  sresultid int(11) NOT NULL default '0',
  questionid int(11) NOT NULL default '0',
  answerid int(11) NOT NULL default '0',
  comment text NOT NULL,
  UNIQUE KEY main (sresultid,questionid,answerid),
  KEY qa (questionid,answerid)
) TYPE=MyISAM;





CREATE TABLE xcart_survey_results (
  sresultid int(11) NOT NULL auto_increment,
  surveyid int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  code char(2) NOT NULL default '',
  from_mail char(1) NOT NULL default '',
  completed char(1) NOT NULL default '',
  as_result varchar(32) NOT NULL default '',
  PRIMARY KEY  (sresultid),
  KEY sil (surveyid,ip,userid),
  KEY sc (surveyid,completed)
) TYPE=MyISAM;





CREATE TABLE xcart_surveys (
  surveyid int(11) NOT NULL auto_increment,
  survey_type char(1) NOT NULL default 'D',
  created_date int(11) NOT NULL default '0',
  valid_from_date int(11) NOT NULL default '0',
  expires_data int(11) NOT NULL default '0',
  publish_results char(1) NOT NULL default '',
  display_on_frontpage char(1) NOT NULL default '',
  event_type char(3) NOT NULL default '',
  event_logic char(1) NOT NULL default 'O',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (surveyid)
) TYPE=MyISAM;





CREATE TABLE xcart_tax_rate_memberships (
  rateid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (rateid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_tax_rates (
  rateid int(11) NOT NULL auto_increment,
  taxid int(11) NOT NULL default '0',
  zoneid int(11) NOT NULL default '0',
  formula varchar(255) NOT NULL default '',
  rate_value decimal(12,3) NOT NULL default '0.000',
  rate_type char(1) NOT NULL default '',
  provider int(11) NOT NULL default '0',
  PRIMARY KEY  (rateid),
  KEY provider (provider),
  KEY tax_rate (taxid,zoneid)
) TYPE=MyISAM;





CREATE TABLE xcart_taxes (
  taxid int(11) NOT NULL auto_increment,
  tax_name varchar(10) NOT NULL default '',
  formula varchar(255) NOT NULL default '',
  address_type char(1) NOT NULL default 'S',
  active char(1) NOT NULL default 'N',
  price_includes_tax char(1) NOT NULL default 'N',
  display_including_tax char(1) NOT NULL default 'N',
  display_info char(1) NOT NULL default '',
  regnumber varchar(255) NOT NULL default '',
  priority int(11) NOT NULL default '0',
  PRIMARY KEY  (taxid),
  UNIQUE KEY tax_name (tax_name),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_temporary_data (
  id varchar(32) NOT NULL default '',
  data text,
  expire int(11) default NULL,
  PRIMARY KEY  (id),
  KEY expire (expire)
) TYPE=MyISAM;





CREATE TABLE xcart_titles (
  titleid int(11) NOT NULL auto_increment,
  title varchar(64) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (titleid),
  KEY ia (titleid,active),
  KEY title (title),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_users_online (
  sessid varchar(40) NOT NULL default '',
  usertype char(1) NOT NULL default '',
  is_registered char(1) NOT NULL default '',
  expiry int(11) NOT NULL default '0',
  PRIMARY KEY  (sessid),
  KEY usertype (usertype),
  KEY iu (is_registered,usertype),
  KEY expiry (expiry)
) TYPE=MyISAM;





CREATE TABLE xcart_variant_backups (
  optionid int(11) NOT NULL default '0',
  variantid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  data text NOT NULL,
  PRIMARY KEY  (optionid,variantid),
  KEY optionid (optionid),
  KEY variantid (variantid),
  KEY productid (productid),
  KEY po (productid,optionid)
) TYPE=MyISAM;





CREATE TABLE xcart_variant_items (
  optionid int(11) NOT NULL default '0',
  variantid int(11) NOT NULL default '0',
  PRIMARY KEY  (optionid,variantid),
  KEY variantid (variantid)
) TYPE=MyISAM;





CREATE TABLE xcart_variants (
  variantid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  avail int(11) NOT NULL default '0',
  weight decimal(12,2) NOT NULL default '0.00',
  productcode varchar(32) NOT NULL default '0',
  def char(1) NOT NULL default '',
  PRIMARY KEY  (variantid),
  UNIQUE KEY productcode (productcode),
  UNIQUE KEY pp (productid,productcode),
  KEY productid (productid),
  KEY avail (avail)
) TYPE=MyISAM;





CREATE TABLE xcart_wishlist (
  wishlistid int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  amount int(11) NOT NULL default '0',
  amount_purchased int(11) NOT NULL default '0',
  options text NOT NULL,
  event_id int(11) NOT NULL default '0',
  object text NOT NULL,
  PRIMARY KEY  (wishlistid),
  KEY userid_product (userid,productid),
  KEY event (event_id)
) TYPE=MyISAM;





CREATE TABLE xcart_xmlmap_extra (
  id int(11) NOT NULL auto_increment,
  url varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM;





CREATE TABLE xcart_xmlmap_lastmod (
  id int(11) NOT NULL default '0',
  type char(1) NOT NULL default '',
  date char(25) NOT NULL default '',
  UNIQUE KEY it (id,type)
) TYPE=MyISAM;





CREATE TABLE xcart_zone_element (
  zoneid int(11) NOT NULL default '0',
  field varchar(36) NOT NULL default '',
  field_type char(1) NOT NULL default '',
  PRIMARY KEY  (zoneid,field,field_type),
  KEY field (field_type,field),
  KEY field_type (zoneid,field_type)
) TYPE=MyISAM;





CREATE TABLE xcart_zones (
  zoneid int(11) NOT NULL auto_increment,
  zone_name varchar(255) NOT NULL default '',
  zone_cache varchar(255) NOT NULL default '',
  provider int(11) NOT NULL default '0',
  PRIMARY KEY  (zoneid),
  KEY zone_name (provider,zone_name)
) TYPE=MyISAM;

