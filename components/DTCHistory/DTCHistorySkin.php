<?php

namespace Xpressengine\Plugins\OSSBoardSkins\Components\DTCHistory;

use App;
use XeStorage;
use Xpressengine\Category\Models\CategoryItem;
use Xpressengine\Config\ConfigEntity;
use Xpressengine\Media\Repositories\ImageRepository;
use Xpressengine\Plugins\Board\Components\Modules\BoardModule;
use Xpressengine\Plugins\Board\Components\Skins\Board\Common\CommonSkin;
use Xpressengine\Plugins\Board\Models\Board;

class DTCHistorySkin extends CommonSkin
{
    protected static $path = 'oss_board_skins/components/DTCHistory';

    public function render()
    {
        $this->data['firstItem'] = null;
        
        $this->data['categoryTree'] = $this->getCategoryItemsTree($this->data['config']);

        if ($this->view === 'index' && request()->get('category_item_id', null) !== null) {
            if ($this->data['paginate']->count() === 1) {
                $firstItem = $this->data['paginate']->first();
                $this->data['firstItem'] = $firstItem;
            }
        }

        if ($this->view === 'index') {
            if (isset($this->data['paginate'])) {
                static::attachThumbnail($this->data['paginate']);
            }
        }
        
        return parent::render();
    }

    public static function attachThumbnail($list)
    {
        foreach ($list as $item) {
            static::bindGalleryThumb($item);
        }
    }

    protected static function bindGalleryThumb(Board $item)
    {
        /** @var \Xpressengine\Media\MediaManager $mediaManager */
        $mediaManager = App::make('xe.media');

        // board gallery thumbnails 에 항목이 없는 경우
        if ($item->thumb == null) {
            // find file by document id
            $files = XeStorage::fetchByFileable($item->id);
            $fileId = '';
            $externalPath = '';
            $thumbnailPath = '';

            if (count($files) == 0) {
                // find file by contents link or path
                $externalPath = static::getImagePathFromContent($item->content);

                // make thumbnail
                $thumbnailPath = $externalPath;
            } else {
                foreach ($files as $file) {
                    if ($mediaManager->is($file) !== true) {
                        continue;
                    }

                    /**
                     * set thumbnail size
                     */
                    $dimension = 'L';

                    $imageRepository = new ImageRepository();
                    $media = $imageRepository->getThumbnail(
                        $mediaManager->make($file),
                        BoardModule::THUMBNAIL_TYPE,
                        $dimension
                    );

                    if ($media === null) {
                        continue;
                    }

                    $fileId = $file->id;
                    $thumbnailPath = $media->url();
                    break;
                }
            }

            $item->board_thumbnail_file_id = $fileId;
            $item->board_thumbnail_external_path = $externalPath;
            $item->board_thumbnail_path = $thumbnailPath;
        } else {
            $item->board_thumbnail_file_id = $item->thumb->board_thumbnail_file_id;
            $item->board_thumbnail_external_path = $item->thumb->board_thumbnail_external_path;
            $item->board_thumbnail_path = $item->thumb->board_thumbnail_path;
        }

        // 없을 경우 출력될 디폴트 이미지 (스킨의 설정으로 뺄 수 있을것 같음)
        if ($item->board_thumbnail_path == '') {
            $item->board_thumbnail_path = asset('assets/core/common/img/default_image_1200x800.jpg');
        }
    }

    protected static function getImagePathFromContent($content)
    {
        $path = '';

        $pattern = '/<img[^>]*src="([^"]+)"[^>][^>]*>/';
        $matches = [];

        preg_match_all($pattern, $content, $matches);
        if (isset($matches[1][0])) {
            $path= $matches[1][0];
        }

        $fullUrl = $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        $path = str_replace($fullUrl, '', $path);
        return $path;
    }
    
    public function getCategoryItemsTree(ConfigEntity $config)
    {
        $items = [];
        if ($config->get('category') === true) {
            $categoryItems = CategoryItem::where('category_id', $config->get('categoryId'))
                ->where('parent_id', null)
                ->orderBy('ordering')->get();

            foreach ($categoryItems as $categoryItem) {
                $categoryItemData = [
                    'value' => $categoryItem->id,
                    'text' => xe_trans($categoryItem->word),
                    'children' => $this->getCategoryItemChildrenData($categoryItem)
                ];
                
                $items[] = $categoryItemData;
            }
        }

        return $items;
    }

    private function getCategoryItemChildrenData(CategoryItem $categoryItem)
    {
        $children = $categoryItem->getChildren();

        if ($children->isEmpty() === true) {
            return [];
        }

        $childrenData = [];
        foreach ($children as $child) {
            $childrenData[] = [
                'value' => $child->id,
                'text' => xe_trans($child->word),
                'children' => $this->getCategoryItemChildrenData($child)
            ];
        }

        return $childrenData;
    }
}
