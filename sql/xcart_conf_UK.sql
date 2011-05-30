DELETE FROM xcart_taxes;
DELETE FROM xcart_tax_rates;
DELETE FROM xcart_tax_rate_memberships;
DELETE FROM xcart_zones;
DELETE FROM xcart_zone_element;
DELETE FROM xcart_product_taxes;
DELETE FROM xcart_languages_alt WHERE name='tax_1';







INSERT INTO xcart_taxes VALUES (1,'VAT17_5','DST+SH','S','Y','N','Y','A','VAT 123-456-789-0000',10);







INSERT INTO xcart_languages_alt VALUES ('en','tax_1','VAT');







INSERT INTO xcart_tax_rates VALUES (1,1,1,'',17.500,'%',1);







INSERT INTO xcart_zones VALUES (1,'UK & European Union','C26',1);
INSERT INTO xcart_zones VALUES (2,'UK zone','C1-S134',1);
INSERT INTO xcart_zones VALUES (4,'European Union (non-UK)','C28',1);







INSERT INTO xcart_zone_element VALUES (1,'AT','C');
INSERT INTO xcart_zone_element VALUES (1,'BE','C');
INSERT INTO xcart_zone_element VALUES (1,'CY','C');
INSERT INTO xcart_zone_element VALUES (1,'CZ','C');
INSERT INTO xcart_zone_element VALUES (1,'DE','C');
INSERT INTO xcart_zone_element VALUES (1,'DK','C');
INSERT INTO xcart_zone_element VALUES (1,'EE','C');
INSERT INTO xcart_zone_element VALUES (1,'ES','C');
INSERT INTO xcart_zone_element VALUES (1,'FI','C');
INSERT INTO xcart_zone_element VALUES (1,'FR','C');
INSERT INTO xcart_zone_element VALUES (1,'GB','C');
INSERT INTO xcart_zone_element VALUES (1,'GR','C');
INSERT INTO xcart_zone_element VALUES (1,'HU','C');
INSERT INTO xcart_zone_element VALUES (1,'IE','C');
INSERT INTO xcart_zone_element VALUES (1,'IT','C');
INSERT INTO xcart_zone_element VALUES (1,'LT','C');
INSERT INTO xcart_zone_element VALUES (1,'LU','C');
INSERT INTO xcart_zone_element VALUES (1,'LV','C');
INSERT INTO xcart_zone_element VALUES (1,'MT','C');
INSERT INTO xcart_zone_element VALUES (1,'NL','C');
INSERT INTO xcart_zone_element VALUES (1,'NO','C');
INSERT INTO xcart_zone_element VALUES (1,'PL','C');
INSERT INTO xcart_zone_element VALUES (1,'PT','C');
INSERT INTO xcart_zone_element VALUES (1,'SE','C');
INSERT INTO xcart_zone_element VALUES (1,'SI','C');
INSERT INTO xcart_zone_element VALUES (1,'SK','C');
INSERT INTO xcart_zone_element VALUES (2,'GB','C');
INSERT INTO xcart_zone_element VALUES (2,'GB_ABd','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_AG','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_AGY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_ARg','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_AV','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_AY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BE','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BEW','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BF','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BK','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BKM','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_BU','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CAn','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CAr','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CB','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CDo','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CDu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CFm','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CGN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CH','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CL','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CLd','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CMN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_COr','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CTy','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CU','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CUL','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CV','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_CW','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DB','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DEN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DF','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DU','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DV','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_DY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_EL','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_ES','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_EX','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_FI','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_FLN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_GL','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_GLA','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_GW','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_GY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_HA','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_HE','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_HF','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_HUN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IS','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsAr','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsBa','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsBe','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsBu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsCa','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsCl','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsCo','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsCu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsEg','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsGi','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsHa','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsIo','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsJu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsLw','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsMu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsNu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsRu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsSc','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsScl','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsSh','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsSk','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsSu','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsTi','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_IsWi','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_KCD','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_KEN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_KKD','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_KRS','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_LAN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_LEI','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_LIN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_LKS','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_LO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_Mdl','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MER','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MG','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MGY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MON','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MOR','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_MX','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NA','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NHB','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NTH','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NTT','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NU','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_NYK','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_OKI','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_OX','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_PEE','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_PEM','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_PER','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_PO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_RAD','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_Rn','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_ROC','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_ROX','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_RUT','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SEL','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SF','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SG','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SH','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SHI','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SR','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_ST','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_STI','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SU','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SUT','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SY','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_SYK','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_TW','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WAR','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WES','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WG','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WIG','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WIt','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WLN','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WM','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WO','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WS','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_WYK','S');
INSERT INTO xcart_zone_element VALUES (2,'GB_YKS','S');
INSERT INTO xcart_zone_element VALUES (4,'AT','C');
INSERT INTO xcart_zone_element VALUES (4,'BE','C');
INSERT INTO xcart_zone_element VALUES (4,'CY','C');
INSERT INTO xcart_zone_element VALUES (4,'CZ','C');
INSERT INTO xcart_zone_element VALUES (4,'DE','C');
INSERT INTO xcart_zone_element VALUES (4,'DK','C');
INSERT INTO xcart_zone_element VALUES (4,'EE','C');
INSERT INTO xcart_zone_element VALUES (4,'ES','C');
INSERT INTO xcart_zone_element VALUES (4,'FI','C');
INSERT INTO xcart_zone_element VALUES (4,'FR','C');
INSERT INTO xcart_zone_element VALUES (4,'GR','C');
INSERT INTO xcart_zone_element VALUES (4,'HU','C');
INSERT INTO xcart_zone_element VALUES (4,'IE','C');
INSERT INTO xcart_zone_element VALUES (4,'IT','C');
INSERT INTO xcart_zone_element VALUES (4,'LT','C');
INSERT INTO xcart_zone_element VALUES (4,'LU','C');
INSERT INTO xcart_zone_element VALUES (4,'LV','C');
INSERT INTO xcart_zone_element VALUES (4,'MK','C');
INSERT INTO xcart_zone_element VALUES (4,'MT','C');
INSERT INTO xcart_zone_element VALUES (4,'NL','C');
INSERT INTO xcart_zone_element VALUES (4,'NO','C');
INSERT INTO xcart_zone_element VALUES (4,'PL','C');
INSERT INTO xcart_zone_element VALUES (4,'PT','C');
INSERT INTO xcart_zone_element VALUES (4,'RO','C');
INSERT INTO xcart_zone_element VALUES (4,'SE','C');
INSERT INTO xcart_zone_element VALUES (4,'SI','C');
INSERT INTO xcart_zone_element VALUES (4,'SK','C');
INSERT INTO xcart_zone_element VALUES (4,'SM','C');



DELETE FROM xcart_config WHERE name='use_counties';

UPDATE xcart_config SET value='Y' WHERE name='enable_shipping';

UPDATE xcart_config SET value='+44-800-555-5555' WHERE name='company_phone';
UPDATE xcart_config SET value='+44-800-555-5555' WHERE name='company_fax';
UPDATE xcart_config SET value='LO' WHERE name='location_state';
UPDATE xcart_config SET value='GB' WHERE name='location_country';

UPDATE xcart_config SET value='LO' WHERE name='default_state';
UPDATE xcart_config SET value='GB' WHERE name='default_country';
UPDATE xcart_config SET value='London' WHERE name='default_city';
UPDATE xcart_config SET value='Y' WHERE name='apply_default_country';

UPDATE xcart_config SET value='&pound;' WHERE name='currency_symbol';
UPDATE xcart_config SET value='&#8364;' WHERE name='alter_currency_symbol';
UPDATE xcart_config SET value='1.50097' WHERE name='alter_currency_rate';

UPDATE xcart_config SET value='N' WHERE name='realtime_shipping';

UPDATE xcart_config SET value='%d/%m/%Y' WHERE name='date_format';

UPDATE xcart_languages SET value='County' WHERE name='lbl_state' AND code='en';
UPDATE xcart_languages SET value='Postal code' WHERE name='lbl_zip_code' AND code='en';
UPDATE xcart_languages SET value='UK TOLL FREE' WHERE name='lbl_phone_1_title' AND code='en';
