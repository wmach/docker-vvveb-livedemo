-- Posts

	-- get all post 

	CREATE PROCEDURE getAll(
		IN start INT,
		IN limit INT,
		IN search CHAR,
		IN username CHAR,
		IN status CHAR,
		IN taxonomy_item_slug CHAR,
		IN post_id ARRAY,
		IN taxonomy_item_id INT,
		-- global	
		IN type CHAR,
		IN site_id INT,
		IN language_id INT,
		-- comment
		IN comment_count INT,
		IN comment_status INT,
		-- archive
		IN year INT,
		IN month INT,
		
		-- return array of posts for posts query
		OUT fetch_all,
		-- return posts count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT pd.*,posts.*,ad.username,ad.display_name,ad.admin_id,ad.email
			@IF isset(:comment_count)
			THEN
				,(SELECT COUNT(c.comment_id) 
						FROM comment c 
					WHERE 
						posts.post_id = c.post_id
					
						@IF isset(:comment_status)
						THEN
							AND c.status = :comment_status
						END @IF
					) 
				
				AS comment_count
			END @IF
			
			
		
		FROM post AS posts
			LEFT JOIN post_content pd ON (
				posts.post_id = pd.post_id 
				
				@IF isset(:language_id)
				THEN
					AND pd.language_id = :language_id
				END @IF

			)  
			LEFT JOIN post_to_site ps ON (posts.post_id = ps.post_id)  
			LEFT JOIN admin ad ON (posts.admin_id = ad.admin_id)  
			
			@IF isset(:taxonomy_item_id) || isset(:taxonomy_item_slug)
			THEN
				LEFT JOIN post_to_taxonomy_item pt ON (posts.post_id = pt.post_id)   
			END @IF			
		
			@IF isset(:search)
			THEN 
				JOIN post_content_search pcs ON (pcs.ROWID = pd.ROWID)   
			END @IF	
		WHERE 1 = 1 
		
			@IF isset(:type)
			THEN
				AND posts.type = :type
			END @IF			
			
			@IF isset(:status) && !empty(:status)
			THEN
				AND posts.status = :status
			@ELSE
				AND posts.status = 'publish'
			END @IF
			
			-- username/author
			@IF isset(:username)
			THEN
				AND posts.admin_id = (SELECT admin_id FROM admin WHERE username = :username LIMIT 1)
			END @IF

            -- search
            @IF isset(:search) && !empty(:search)
			THEN 
				-- AND pd.name LIKE CONCAT('%',:search,'%')
				-- AND MATCH(pd.name, pd.content) AGAINST(:search)
				-- AND (pcs.name MATCH :search OR pcs.content MATCH :search) 
				AND (pcs.content MATCH :search) 
        	END @IF	     
            
            -- post_id
			@IF isset(:post_id) && count(:post_id) > 0
			THEN 
			
				AND posts.post_id IN (:post_id)
				
			END @IF		

			@IF isset(:site_id)
			THEN
				AND ps.site_id = :site_id
			END @IF

			
			@IF isset(:taxonomy_item_id)
			THEN
				AND pt.taxonomy_item_id = :taxonomy_item_id
			END @IF	
			
			
			@IF isset(:taxonomy_item_slug)
			THEN
				AND pt.taxonomy_item_id = (SELECT taxonomy_item_id FROM taxonomy_item_content WHERE slug = :taxonomy_item_slug LIMIT 1)
			END @IF

			-- month
			@IF isset(:month) && !empty(:month)
			THEN
				AND strftime('%M', date_added) = :month
			END @IF					

			-- year
			@IF isset(:year) && !empty(:year)
			THEN
				AND strftime('%Y', date_added) = :year
			END @IF					

			-- order by
			@IF isset(:order_by)
			THEN
				ORDER BY posts.$order_by $direction		
			@ELSE
				ORDER BY posts.post_id DESC
			END @IF		
			
			-- limit
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;


		-- SELECT FOUND_ROWS() as count;
		SELECT count(*) FROM (
			
			@SQL_COUNT(posts.post_id, post) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;
	 
	END
	

	-- get one post

	CREATE PROCEDURE get(
		IN post_id INT,
		IN slug CHAR,
		IN language_id INT,
		IN comment_count INT,
		IN comment_status INT,
		IN type CHAR,
		
		OUT fetch_row,
		OUT fetch_row,
		OUT fetch_all,
	)
	BEGIN

		SELECT _.*,pd.*,ad.admin_id,ad.username,ad.display_name,ad.email
		
		@IF isset(:comment_count)
			THEN
				,(SELECT COUNT(c.comment_id) 
						FROM comment c 
					WHERE 
						_.post_id = c.post_id
					
						@IF isset(:comment_status)
						THEN
							AND c.status = :comment_status
						END @IF
					) 
				
				AS comment_count
			END @IF 
			
			FROM post AS _
			LEFT JOIN post_content pd ON 
				(
					_.post_id = pd.post_id 

					@IF isset(:language_id)
					THEN
						AND pd.language_id = :language_id
					END @IF
				)  

			LEFT JOIN admin ad ON (_.admin_id = ad.admin_id)  				
			
		WHERE 1 = 1

            @IF isset(:slug)
			THEN 
				AND pd.slug = :slug 
        	END @IF			

            @IF isset(:post_id)
			THEN
                AND _.post_id = :post_id
        	END @IF			
			
			@IF isset(:type)
			THEN
                AND _.type = :type
        	END @IF			

        LIMIT 1; 
		
		-- content
		SELECT *, language_id as array_key -- (underscore) _ column means that this column (language_id) value will be used as array key when adding row to result array
			FROM post_content AS post_content 
		WHERE post_id = @result.post_id;
		
	 
		-- meta
		SELECT `key` as array_key,`value` as array_value FROM post_meta as _
			WHERE _.post_id = @result.post_id;	 
	 
	END

	-- Add new post

	CREATE PROCEDURE add(
		IN post ARRAY,
		IN post_id INT,
		IN site_id INT,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:post_data  = @FILTER(:post, post);
		
		
		INSERT INTO post 
			
			( @KEYS(:post_data) )
			
	  	VALUES ( :post_data );

		:post.post_content  = @FILTER(:post.post_content, post_content, false, true);


		@EACH(:post.post_content) 
			INSERT INTO post_content 
		
				( @KEYS(:each), post_id)
			
			VALUES ( :each, @result.post);

		@EACH(:post.taxonomy_item) 
			INSERT INTO post_to_taxonomy_item 
		
				( `taxonomy_item_id`, post_id)
			
			VALUES ( :each, @result.post)
			ON CONFLICT(`post_id`,`taxonomy_item_id`) DO UPDATE SET `taxonomy_item_id` = :each;

		INSERT INTO post_to_site 
		
			( `post_id`, `site_id` )
			
		VALUES ( @result.post, :site_id );			

	END

	-- Edit post

	CREATE PROCEDURE edit(
		IN post ARRAY,
		IN post_id INT,
		IN site_id INT,
		OUT affected_rows
	)
	BEGIN
		BEGIN TRANSACTION;
		:post.post_content  = @FILTER(:post.post_content, post_content, false, true);
		
		@EACH(:post.post_content) 
			INSERT INTO post_content 
		
				( @KEYS(:each), post_id)
			
			VALUES ( :each, :post_id)

			ON CONFLICT(post_id, language_id) DO UPDATE SET @LIST(:each);


		-- @IF isset(:post.taxonomy_item) 

			-- DELETE FROM post_to_taxonomy_item WHERE post_id = :post_id;

			@EACH(:post.taxonomy_item) 
				INSERT INTO post_to_taxonomy_item 
			
					(taxonomy_item_id, post_id)
				
				VALUES ( :each, :post_id)
				ON CONFLICT(`post_id`,`taxonomy_item_id`) DO UPDATE SET `taxonomy_item_id` = :each;

		-- END @IF

		INSERT OR IGNORE INTO post_to_site 
		
			( `post_id`, `site_id` )
			
		VALUES ( :post_id, :site_id );			


		-- allow only table fields and set defaults for missing values
		@FILTER(:post, post);
	
		@IF !empty(:post) 
		THEN
			UPDATE post 
				
				SET @LIST(:post) 
				
			WHERE post_id = :post_id
		END @IF;
		

		COMMIT;
	END
	
	
	-- Delete post

	CREATE PROCEDURE delete(
		IN  post_id ARRAY,
		IN  site_id INT,
		OUT affected_rows
	)
	BEGIN
		
		DELETE FROM post_to_taxonomy_item WHERE post_id IN (:post_id);
		DELETE FROM post_to_site WHERE post_id IN (:post_id);
		DELETE FROM post_content WHERE post_id IN (:post_id);
		DELETE FROM post WHERE post_id IN (:post_id);
	 
	END
	
	
	
	-- Get tags

	CREATE PROCEDURE postTags(
		IN  id_post INT,
		OUT affected_rows
	)
	BEGIN
	END
	
	
	-- Get categories

	CREATE PROCEDURE postCategories(
		IN  id_post INT,
		OUT affected_rows
	)
	BEGIN
	END
	
	-- Add categories
	CREATE PROCEDURE setPostTaxonomy(
		IN  id_post INT,
		IN  taxonomy_item ARRAY,
		OUT affected_rows
	)
	BEGIN
	
		DELETE FROM post_to_taxonomy_item WHERE post_id = :id_post;
	
		@EACH(:taxonomy_item) 
			INSERT IGNORE INTO post_to_taxonomy_item 
		
				( `post_id`, `taxonomy_item_id`)
			
			VALUES ( :post_id, :each);
	END


	-- Get archives

	CREATE PROCEDURE getArchives(
		IN start INT,
		IN limit INT,
		IN interval CHAR,
		IN type CHAR,
	)
	BEGIN
	
		SELECT 
			strftime('%Y',date_added) AS `year` 
			
			@IF isset(:interval) AND (:interval == "month" || :interval == "day")
			THEN
				,strftime('%M',date_added) AS `month` 
			END @IF

			@IF isset(:interval) AND :interval == "day"
			THEN
				,strftime('%D',date_added) AS `day` 
			END @IF
			
			,count(archives.post_id) as count 
			
		FROM post AS archives
		LEFT JOIN post_to_site ps ON (archives.post_id = ps.post_id) 
		
		WHERE 1 = 1 
			
			@IF isset(:type)
			THEN
				AND type = :type
			END @IF
			
			@IF isset(:site_id)
			THEN
				AND ps.site_id = :site_id
			END @IF

		
		GROUP BY 
			strftime('%Y',date_added)
			
			@IF isset(:interval) AND (:interval == "month" || :interval == "day")
			THEN
				,strftime('%M',date_added)
			END @IF

			@IF isset(:interval) AND :interval == "day"
			THEN
				,strftime('%D',date_added)
			END @IF
			
		ORDER BY 			
			strftime('%Y',date_added)
			
			@IF isset(:interval) AND (:interval == "month" || :interval == "day")
			THEN
				,strftime('%M',date_added)
			END @IF

			@IF isset(:interval) AND :interval == "day"
			THEN
				,strftime('%D',date_added)
			END @IF


		-- limit
		@IF isset(:limit) AND :limit > 0
		THEN
			LIMIT :start, :limit
		END @IF;		
	
	END
