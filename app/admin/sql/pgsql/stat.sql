-- Stats

	-- get all stat 

	CREATE PROCEDURE getStats(
		-- variables
		IN  language_id INT,
		IN  site_id INT,
		IN 	post_id INT,
        	IN 	user_id INT,
		
		-- interval
		IN 	start_date DATE,
		IN 	end_date DATE,

		-- return
		OUT fetch_all, -- orders
		OUT fetch_all  -- users
	)
	BEGIN
	
			-- orders

			SELECT COUNT(*) AS orders, DATE(date_added) as "date" FROM "order" AS orders
			
				LEFT JOIN order_status AS os ON (orders.order_status_id = os.order_status_id AND os.language_id = :language_id) 
				
			WHERE 1 = 1 
			
				AND orders.site_id = :site_id
				
				@IF isset(:order_status)
				THEN 
					AND os.name = :order_status
				END @IF		
			
				-- AND DATE(orders.date_added) >= DATE(:start_date)
			
			GROUP BY DATE(orders.date_added) ORDER BY date;
			
			
			-- users

			SELECT COUNT(*) AS users, DATE(date_added) as "date" FROM "user" AS users
			
			WHERE 1 = 1 
			
				-- AND users.site_id = :site_id
		
			
				-- AND DATE(date_added) >= DATE(:start_date)
			
			GROUP BY DATE(date_added) ORDER BY date;			

	END
	
