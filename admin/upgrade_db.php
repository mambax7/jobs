<?php
/**
 * Jobs for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package     jobs
 * @author      John Mordo aka jlm69 (www.jlmzone.com )
 * @author      XOOPS Development Team
 */

use XoopsModules\Jobs;

include __DIR__ . '/admin_header.php';
/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();

$db = \XoopsDatabaseFactory::getDatabaseConnection();

$sqls = [
    "ALTER TABLE " . $db->prefix('jobs_categories') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_companies') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_created_resumes') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_listing') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_price') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_region') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_replies') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_resume') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_res_categories') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_type') . " ENGINE=InnoDB;",
    "ALTER TABLE " . $db->prefix('jobs_pictures') . " ENGINE=InnoDB;",

    "ALTER TABLE " . $db->prefix('jobs_listing') . " ADD FOREIGN KEY (cid) REFERENCES " . $db->prefix('jobs_categories') . "(cid) ON DELETE CASCADE;",
    "ALTER TABLE " . $db->prefix('jobs_resume') . " ADD FOREIGN KEY (cid) REFERENCES " . $db->prefix('jobs_res_categories') . "(cid) ON DELETE CASCADE;",
    "ALTER TABLE " . $db->prefix('jobs_replies') . " ADD FOREIGN KEY (lid) REFERENCES " . $db->prefix('jobs_listing') . "(lid) ON DELETE CASCADE;",
    "ALTER TABLE " . $db->prefix('jobs_created_resumes') . " ADD FOREIGN KEY (lid) REFERENCES " . $db->prefix('jobs_listing') . "(lid) ON DELETE CASCADE;",
    "ALTER TABLE " . $db->prefix('jobs_pictures') . " ADD FOREIGN KEY (lid) REFERENCES " . $db->prefix('jobs_listing') . "(lid) ON DELETE CASCADE;",

    "ALTER TABLE " . $db->prefix('jobs_listing') . " ADD INDEX (cid);",
    "ALTER TABLE " . $db->prefix('jobs_resume') . " ADD INDEX (cid);",
    "ALTER TABLE " . $db->prefix('jobs_replies') . " ADD INDEX (lid);",
    "ALTER TABLE " . $db->prefix('jobs_created_resumes') . " ADD INDEX (lid);",
    "ALTER TABLE " . $db->prefix('jobs_pictures') . " ADD INDEX (lid);"
];

foreach ($sqls as $sql) {
    if (!$db->queryF($sql)) {
        echo $db->error();
    }
}

echo "Database upgrade complete.";

include __DIR__ . '/admin_footer.php';
