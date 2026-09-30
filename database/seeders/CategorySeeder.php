<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedNodes($this->tree(), null);
        });
    }

    protected function seedNodes(array $nodes, ?int $parentId): void
    {
        $sort = 0;

        foreach ($nodes as $node) {
            $sort += 10;

            // A node is either "Name" (leaf) or ['name' => ..., 'children' => [...]]
            if (is_string($node)) {
                $name     = $node;
                $children = [];
            } else {
                $name     = $node['name'];
                $children = $node['children'] ?? [];
            }

            $slug = Str::slug($name);

            $parentPath = $parentId
                ? Category::whereKey($parentId)->value('path')
                : null;

            $level = $parentId
                ? ((int) Category::whereKey($parentId)->value('level')) + 1
                : 1;

            $path = $parentPath ? $parentPath . '/' . $slug : $slug;

            $category = Category::updateOrCreate(
                ['path' => $path],
                [
                    'parent_id'  => $parentId,
                    'name'       => $name,
                    'slug'       => $slug,
                    'level'      => $level,
                    'sort_order' => $sort,
                    'is_active'  => true,
                ]
            );

            if (! empty($children)) {
                $this->seedNodes($children, $category->id);
            }
        }
    }

    protected function tree(): array
    {
        return [
            [
                'name' => 'Laptops',
                'children' => [
                    ['name' => 'Lenovo Laptops', 'children' => [
                        'Lenovo IdeaPads',
                        'Lenovo V Series',
                        'Lenovo Yoga Series',
                        'Lenovo ThinkPad T Series',
                        'Lenovo ThinkPad E Series',
                        'Lenovo ThinkBook',
                    ]],
                    ['name' => 'Dell Laptops', 'children' => [
                        'Dell Latitude',
                        'Dell Vostro',
                        'Dell XPS',
                        'Dell Inspiron',
                        'Dell Precision',
                    ]],
                    ['name' => 'HP Laptops', 'children' => [
                        'HP ProBook',
                        'HP EliteBook',
                        'HP Pavilion',
                        'HP Envy',
                        'HP Victus',
                        'HP Omen',
                    ]],
                    ['name' => 'Asus / Apple', 'children' => [
                        'Asus VivoBook',
                        'Asus ExpertBook',
                        'Asus ZenBook',
                        'Asus ROG',
                        'Apple MacBook Air',
                        'Apple MacBook Pro',
                    ]],
                    ['name' => 'Gaming Laptops', 'children' => [
                        'Lenovo Legion',
                        'Dell Alienware',
                        'HP Omen Gaming',
                        'Asus ROG Gaming',
                        'Acer Nitro',
                        'MSI Gaming',
                    ]],
                ],
            ],
            [
                'name' => 'Desktops',
                'children' => [
                    ['name' => 'Lenovo Desktops', 'children' => [
                        'Lenovo ThinkCentre',
                        'Lenovo IdeaCentre',
                    ]],
                    ['name' => 'Dell Desktops', 'children' => [
                        'Dell OptiPlex',
                        'Dell Vostro Desktop',
                        'Dell XPS Desktop',
                    ]],
                    ['name' => 'HP Desktops', 'children' => [
                        'HP ProDesk',
                        'HP EliteDesk',
                        'HP Pavilion Desktop',
                    ]],
                    ['name' => 'Custom / Gaming PCs', 'children' => [
                        'Gaming PCs',
                        'Workstations',
                    ]],
                    ['name' => 'All-in-One PCs', 'children' => [
                        'Lenovo AIO',
                        'Dell AIO',
                        'HP AIO',
                    ]],
                ],
            ],
            [
                'name' => 'Components',
                'children' => [
                    ['name' => 'Processors', 'children' => ['Intel', 'AMD']],
                    ['name' => 'Motherboards', 'children' => ['ASUS', 'MSI', 'Gigabyte', 'ASRock']],
                    ['name' => 'RAM / Memory', 'children' => ['DDR4', 'DDR5']],
                    ['name' => 'Storage (SSD/HDD)', 'children' => ['SSD', 'HDD', 'NVMe']],
                    ['name' => 'Graphics Cards', 'children' => ['NVIDIA', 'AMD Radeon']],
                    ['name' => 'Power Supplies', 'children' => ['Corsair', 'EVGA', 'Seasonic']],
                    ['name' => 'Cases', 'children' => ['ATX', 'Micro-ATX', 'Mini-ITX']],
                    ['name' => 'Cooling', 'children' => ['Air Coolers', 'AIO Liquid Coolers', 'Case Fans']],
                ],
            ],
            [
                'name' => 'Servers & Networking',
                'children' => [
                    ['name' => 'Servers', 'children' => ['Rack Servers', 'Tower Servers', 'Blade Servers']],
                    ['name' => 'Routers', 'children' => ['Cisco', 'TP-Link', 'Ubiquiti']],
                    ['name' => 'Switches', 'children' => ['Managed Switches', 'Unmanaged Switches']],
                    ['name' => 'Access Points', 'children' => ['Ubiquiti AP', 'TP-Link AP', 'Cisco AP']],
                    ['name' => 'Firewalls', 'children' => ['Fortinet', 'Cisco Firepower', 'pfSense']],
                    ['name' => 'NAS', 'children' => ['Synology', 'QNAP', 'WD']],
                ],
            ],
            [
                'name' => 'Accessories',
                'children' => [
                    ['name' => 'Mice', 'children' => ['Logitech', 'HP', 'Dell', 'UGREEN']],
                    ['name' => 'Keyboards', 'children' => ['Logitech', 'HP', 'Dell']],
                    ['name' => 'Headsets', 'children' => ['Logitech', 'Jabra', 'JBL', 'Soundcore']],
                    ['name' => 'Laptop Stands', 'children' => ['UGREEN', 'Logitech']],
                    ['name' => 'Monitors', 'children' => ['Dell Monitor', 'HP Monitor', 'Samsung', 'LG']],
                    ['name' => 'Docking Stations', 'children' => ['Lenovo Dock', 'Dell Dock', 'HP Dock']],
                    ['name' => 'Bags & Cases', 'children' => ['Targus', 'Samsonite']],
                    ['name' => 'Cables & Adapters', 'children' => ['UGREEN Cables', 'Belkin']],
                ],
            ],
            [
                'name' => 'Printers',
                'children' => [
                    ['name' => 'Inkjet Printers', 'children' => ['HP Inkjet', 'Canon Inkjet', 'Epson Inkjet']],
                    ['name' => 'Laser Printers', 'children' => ['HP Laser', 'Brother Laser', 'Canon Laser']],
                    ['name' => 'All-in-One Printers', 'children' => ['HP AIO Printer', 'Epson AIO Printer', 'Canon AIO Printer']],
                    ['name' => 'Label Printers', 'children' => ['Brother Label', 'DYMO']],
                    ['name' => 'Scanners', 'children' => ['Canon Scanner', 'Epson Scanner', 'Fujitsu']],
                    ['name' => 'Toner & Ink', 'children' => ['HP Toner', 'Canon Toner', 'Epson Ink']],
                ],
            ],
        ];
    }
}