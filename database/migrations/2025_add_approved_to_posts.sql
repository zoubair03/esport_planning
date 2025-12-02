-- Migration: add approved flag to posts
ALTER TABLE `posts`
  ADD COLUMN `approved` TINYINT(1) NOT NULL DEFAULT 0 AFTER `content`;

-- Mark existing posts as approved so live content remains visible
UPDATE `posts` SET `approved` = 1 WHERE `approved` IS NULL OR `approved` = 0; -- adjust as needed

