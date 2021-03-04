<?php

namespace App\Jobs;

use App\Models\AssetMovement;
use App\Models\AssetSerialStocks;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueueAssetMovement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assetmovement = AssetMovement::find($this->id);
        // dd($assetmovement);
            //Update Produk Warehouse
            if($assetmovement->type == 1){
                // dd($assetmovement);
                $assetserialstock = AssetSerialStocks::where('asset_serial_id',$assetmovement->asset_serial_id)
                                                ->where('expired_date',$assetmovement->expired_date)
                                                ->first();
                if(!$assetserialstock){
                    $assetserialstock = AssetSerialStocks::create([
                        'asset_serial_id'  => $assetmovement->asset_serial_id,
                        'stock' 	    => 0,
                        'expired_date' 	    => $assetmovement->expired_date,
                    ]);
                }
                $assetserialstock->stock = $assetserialstock->stock + $assetmovement->qty;
                $assetserialstock->save();
            }
            if($assetmovement->type == 0){
                $assetserialstock = AssetSerialStocks::where('asset_serial_id',$assetmovement->asset_serial_id)
                                                ->where('expired_date',$assetmovement->expired_date)
                                                ->first();
                // dd($assetserialstock);
                if(!$assetserialstock){
                    $assetserialstock = AssetSerialStocks::create([
                        'asset_serial_id'  => $assetmovement->asset_serial_id,
                        'stock' 	    => 0,
                        'expired_date' 	    => $assetmovement->expired_date,
                    ]);
                }
                $assetserialstock->stock = $assetserialstock->stock - $assetmovement->qty;
                $assetserialstock->save();
            }
    }
}
