<?php

namespace Database\Seeders;

use App\Models\Line;
use App\Models\ProductType;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

/**
 * Demo dataset for modular-construction manufacturing.
 *
 * Adds production lines, module-oriented product types, and representative
 * work orders for the core flow: frame -> MEP -> interior -> facade -> dispatch.
 */
class ModularConstructionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $lines = $this->seedLines();
        $products = $this->seedProductTypes();
        $this->seedWorkOrders($lines, $products);
    }

    private function seedLines(): array
    {
        $defs = [
            ['code' => 'MC-FRAME', 'name' => 'Steel Frame Assembly', 'description' => 'Frame welding, geometry control, anti-corrosion prep'],
            ['code' => 'MC-MEP', 'name' => 'MEP Installation', 'description' => 'Electrical, plumbing, HVAC rough-in and pressure tests'],
            ['code' => 'MC-INT', 'name' => 'Interior Finishing', 'description' => 'Insulation, gypsum, painting, flooring, built-in furniture'],
            ['code' => 'MC-FACADE', 'name' => 'Facade & Envelope', 'description' => 'External cladding, windows, doors, weatherproofing'],
            ['code' => 'MC-QA', 'name' => 'Final QA & Dispatch', 'description' => 'Punch-list closure, acceptance checks, transport preparation'],
        ];

        $result = [];
        foreach ($defs as $def) {
            $line = Line::updateOrCreate(
                ['code' => $def['code']],
                [
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'is_active' => true,
                ],
            );
            $result[$def['code']] = $line;
        }

        return $result;
    }

    private function seedProductTypes(): array
    {
        $defs = [
            ['code' => 'MOD-STD-24', 'name' => 'Residential Module 24m2', 'description' => 'Single residential module with bathroom core', 'unit_of_measure' => 'module'],
            ['code' => 'MOD-STD-36', 'name' => 'Residential Module 36m2', 'description' => 'Expanded residential module with full kitchen zone', 'unit_of_measure' => 'module'],
            ['code' => 'MOD-OFFICE', 'name' => 'Office Module', 'description' => 'Commercial office module with raised flooring and cable trays', 'unit_of_measure' => 'module'],
            ['code' => 'MOD-SAN', 'name' => 'Sanitary Core Module', 'description' => 'Pre-fitted sanitary module for multi-storey residential projects', 'unit_of_measure' => 'module'],
        ];

        $result = [];
        foreach ($defs as $def) {
            $product = ProductType::updateOrCreate(
                ['code' => $def['code']],
                [
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'unit_of_measure' => $def['unit_of_measure'],
                    'is_active' => true,
                ],
            );
            $result[$def['code']] = $product;
        }

        return $result;
    }

    private function seedWorkOrders(array $lines, array $products): void
    {
        $orders = [
            [
                'order_no' => 'MC-2026-001',
                'line' => 'MC-FRAME',
                'product' => 'MOD-STD-24',
                'planned_qty' => 12,
                'produced_qty' => 5,
                'status' => WorkOrder::STATUS_IN_PROGRESS,
                'priority' => 5,
                'due_date' => now()->addDays(6),
                'planned_start_at' => now()->subDay()->setTime(7, 30),
                'planned_end_at' => now()->addDays(5)->setTime(18, 0),
                'description' => 'Phase A housing batch, line balancing for steel frame cells',
            ],
            [
                'order_no' => 'MC-2026-002',
                'line' => 'MC-MEP',
                'product' => 'MOD-SAN',
                'planned_qty' => 18,
                'produced_qty' => 0,
                'status' => WorkOrder::STATUS_ACCEPTED,
                'priority' => 4,
                'due_date' => now()->addDays(10),
                'planned_start_at' => now()->addDays(2)->setTime(8, 0),
                'planned_end_at' => now()->addDays(9)->setTime(17, 0),
                'description' => 'Sanitary modules for Block C with mandatory pressure-test checkpoints',
            ],
            [
                'order_no' => 'MC-2026-003',
                'line' => 'MC-INT',
                'product' => 'MOD-STD-36',
                'planned_qty' => 10,
                'produced_qty' => 0,
                'status' => WorkOrder::STATUS_PENDING,
                'priority' => 3,
                'due_date' => now()->addDays(14),
                'planned_start_at' => now()->addDays(4)->setTime(7, 0),
                'planned_end_at' => now()->addDays(13)->setTime(16, 30),
                'description' => 'Interior completion wave linked to supplier lot arrivals',
            ],
            [
                'order_no' => 'MC-2026-004',
                'line' => 'MC-FACADE',
                'product' => 'MOD-OFFICE',
                'planned_qty' => 8,
                'produced_qty' => 8,
                'status' => WorkOrder::STATUS_DONE,
                'priority' => 2,
                'due_date' => now()->subDays(1),
                'planned_start_at' => now()->subDays(8)->setTime(8, 0),
                'planned_end_at' => now()->subDays(2)->setTime(15, 30),
                'completed_at' => now()->subDays(2)->setTime(15, 45),
                'description' => 'Office park modules, facade package completed and signed off',
            ],
            [
                'order_no' => 'MC-2026-005',
                'line' => 'MC-QA',
                'product' => 'MOD-STD-24',
                'planned_qty' => 6,
                'produced_qty' => 2,
                'status' => WorkOrder::STATUS_IN_PROGRESS,
                'priority' => 4,
                'due_date' => now()->addDays(4),
                'planned_start_at' => now()->setTime(9, 0),
                'planned_end_at' => now()->addDays(3)->setTime(18, 0),
                'description' => 'Final QA gate before transport, punch-list closure in progress',
            ],
        ];

        foreach ($orders as $order) {
            WorkOrder::updateOrCreate(
                ['order_no' => $order['order_no']],
                [
                    'line_id' => $lines[$order['line']]->id,
                    'product_type_id' => $products[$order['product']]->id,
                    'planned_qty' => $order['planned_qty'],
                    'produced_qty' => $order['produced_qty'],
                    'status' => $order['status'],
                    'priority' => $order['priority'],
                    'due_date' => $order['due_date'],
                    'planned_start_at' => $order['planned_start_at'],
                    'planned_end_at' => $order['planned_end_at'],
                    'completed_at' => $order['completed_at'] ?? null,
                    'description' => $order['description'],
                ],
            );
        }
    }
}
