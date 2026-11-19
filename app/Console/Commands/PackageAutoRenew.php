<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PackageAutoRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:package-auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-renew investment packages that expire today, extend the cycle, and credit ROI profit to user balance';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $today = Carbon::today()->toDateString();

        // Find all orders that:
        // 1. Are not soft-deleted
        // 2. Have package_status = 1 (active)
        // 3. Have auto_renew = 1 (enabled)
        // 4. Expire today
        // Eager-load user and package to avoid N+1
        $orders = Order::with(['user', 'package'])
            ->where('package_status', 1)
            ->where('auto_renew', 1)
            ->whereDate('invest_expire', $today)
            ->get();

        if ($orders->isEmpty()) {
            $this->info("No packages to renew today ({$today}).");
            Log::info("PackageAutoRenew: No packages to renew on {$today}.");
            return;
        }

        $renewedCount = 0;

        foreach ($orders as $order) {
            try {
                $user = $order->user;
                $package = $order->package;

                // Guard: skip if related records are missing
                if (!$user || !$package) {
                    $this->warn("Order #{$order->id} skipped — missing user or package.");
                    Log::warning("PackageAutoRenew: Order #{$order->id} skipped — user or package not found.");
                    continue;
                }

                $duration = (int) $order->package_duration;

                if ($duration <= 0) {
                    $this->warn("Order #{$order->id} skipped — invalid duration ({$duration}).");
                    Log::warning("PackageAutoRenew: Order #{$order->id} skipped — invalid duration ({$duration}).");
                    continue;
                }

                // ── Profit Calculation ──────────────────────────────────────
                // Profit = user's current balance × (package ROI% / 100)
                $roi = (float) $package->roi;          // e.g. 5.00 means 5%
                $profit = round($user->balance * ($roi / 100), 2);
                // ────────────────────────────────────────────────────────────

                // 1. Credit profit to user balance & track total earned profit
                $user->increment('balance', $profit);  // spendable balance
                $user->increment('profit', $profit);   // lifetime profit tracker

                // 2. Extend the investment cycle
                $newStart = Carbon::today();
                $newExpire = Carbon::today()->addDays($duration);

                $order->update([
                    'invest_start' => $newStart->toDateString(),
                    'invest_expire' => $newExpire->toDateString(),
                ]);

                $renewedCount++;

                $this->info(
                    "Order #{$order->id} | User: {$user->name} | " .
                    "ROI: {$roi}% | Profit: +{$profit} | " .
                    "New cycle: {$newStart->toDateString()} → {$newExpire->toDateString()}"
                );

                Log::info(
                    "PackageAutoRenew: Order #{$order->id} (user_id={$user->id}) renewed. " .
                    "ROI={$roi}%, Profit credited=+{$profit}, " .
                    "New balance={$user->fresh()->balance}, Total profit={$user->fresh()->profit}, " .
                    "Cycle: {$newStart->toDateString()} → {$newExpire->toDateString()} ({$duration} days)."
                );

            } catch (\Throwable $e) {
                $this->error("Order #{$order->id} failed: {$e->getMessage()}");
                Log::error("PackageAutoRenew: Order #{$order->id} renewal failed. Error: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$renewedCount} package(s) renewed.");
        Log::info("PackageAutoRenew: Completed. {$renewedCount} package(s) renewed on {$today}.");
    }
}
