select 
(select count(*) from btxmarkethistory) AS btxmarkethistorycount,
(select pg_relation_size('btxmarkethistory')) AS martkethistorysize,
(select count(*) from btxcoinmarkethistorydetails) AS marketdetailscount,
(select pg_relation_size('btxcoinmarkethistorydetails')) AS martketdetailssize;