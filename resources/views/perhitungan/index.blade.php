@include('layouts.header_admin')
<?php
\App\Models\PerhitunganModel::hapus_hasil();

//Matrix Keputusan (X)
$matriks_x = array();
foreach($alternatifs as $alternatif):
    foreach($kriterias as $kriteria):
        
        $id_alternatif = $alternatif->id_alternatif;
        $id_kriteria = $kriteria->id_kriteria;
        
        $data_pencocokan = \App\Models\PerhitunganModel::data_nilai($id_alternatif, $id_kriteria);
        if(!empty($data_pencocokan['nilai'])){$nilai = $data_pencocokan['nilai'];}else{$nilai = 0;}
        
        $matriks_x[$id_kriteria][$id_alternatif] = $nilai;
    endforeach;
endforeach;

//Matriks Ternormalisasi (R)
$matriks_r = array();
foreach($matriks_x as $id_kriteria => $penilaians):
	
	$jumlah_kuadrat = 0;
	foreach($penilaians as $penilaian):
		$jumlah_kuadrat += pow($penilaian, 2);
	endforeach;
	$akar_kuadrat = sqrt($jumlah_kuadrat);
	
	foreach($penilaians as $id_alternatif => $penilaian):
		$matriks_r[$id_kriteria][$id_alternatif] = $penilaian / $akar_kuadrat;
	endforeach;
	
endforeach;

//Matriks Normalisasi Terbobot
$matriks_rb = array();
foreach($alternatifs as $alternatif):
	foreach($kriterias as $kriteria):
		
		$bobot = $kriteria->bobot;
		$id_alternatif = $alternatif->id_alternatif;
		$id_kriteria = $kriteria->id_kriteria;
		
		$nilai_r = $matriks_r[$id_kriteria][$id_alternatif];
		$matriks_rb[$id_kriteria][$id_alternatif] = $bobot * $nilai_r;

	endforeach;
endforeach;

//Nilai Yi
$nilai_y_max = array();
$nilai_y_min = array();
foreach($alternatifs as $alternatif):
	$total_max = 0;
	$total_min = 0;
	foreach($kriterias as $kriteria):

		$id_alternatif = $alternatif->id_alternatif;
		$id_kriteria = $kriteria->id_kriteria;
		$type_kriteria = $kriteria->jenis;
		
		$nilai_rb = $matriks_rb[$id_kriteria][$id_alternatif];
		
		if($type_kriteria == 'Benefit'):
			$total_max += $nilai_rb;
		elseif($type_kriteria == 'Cost'):
			$total_min += $nilai_rb;
		endif;
	endforeach;
	$nilai_y_max[$id_kriteria][$id_alternatif] = $total_max;
	$nilai_y_min[$id_kriteria][$id_alternatif] = $total_min;
endforeach;
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-calculator"></i> Data Perhitungan</h1>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-table"></i> Matrix Keputusan (X)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-info text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria->kode_kriteria ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif->nama ?></td>
						<?php
						foreach ($kriterias as $kriteria):
							$id_alternatif = $alternatif->id_alternatif;
							$id_kriteria = $kriteria->id_kriteria;
							echo '<td>';
							echo $matriks_x[$id_kriteria][$id_alternatif];
							echo '</td>';
						endforeach
						?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-table"></i> Bobot Preferensi (W)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-info text-white">
					<tr align="center">
						<?php foreach ($kriterias as $kriteria): ?>
						<th><?= $kriteria->kode_kriteria ?> (<?= $kriteria->jenis ?>)</th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<?php foreach ($kriterias as $kriteria): ?>
						<td>
						<?php 
						echo $kriteria->bobot;
						?>
						</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-table"></i> Matriks Ternormalisasi (R)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-info text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria->kode_kriteria ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif->nama ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif->id_alternatif;
							$id_kriteria = $kriteria->id_kriteria;
							echo '<td>';
							echo round($matriks_r[$id_kriteria][$id_alternatif],4);
							echo '</td>';
						endforeach;
						?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-table"></i> Matriks Normalisasi Terbobot</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-info text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria->kode_kriteria ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif->nama ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif->id_alternatif;
							$id_kriteria = $kriteria->id_kriteria;
							echo '<td>';
							echo round($matriks_rb[$id_kriteria][$id_alternatif],4);
							echo '</td>';
						endforeach;
						?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-table"></i> Menghitung Nilai Yi</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-info text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<th>Maximun ( 
						<?php foreach ($kriterias as $kriteria):
							if ($kriteria->jenis=="Benefit"){
								echo $kriteria->kode_kriteria." ";
							}
						endforeach 
						?>)
						</th>
						<th>Minimum ( 
						<?php foreach ($kriterias as $kriteria):
							if ($kriteria->jenis=="Cost"){
								echo $kriteria->kode_kriteria." ";
							}
						endforeach 
						?>)
						</th>
						<th>Yi = Max - Min</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif->nama ?></td>
						<?php			
						$total_max = 0;
						$total_min = 0;
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif->id_alternatif;
							$id_kriteria = $kriteria->id_kriteria;
							$nilai_rb = round($matriks_rb[$id_kriteria][$id_alternatif],4);
							if ($kriteria->jenis=="Benefit"){
								$total_max += $nilai_rb;
							}else{
								$total_min += $nilai_rb;
							}
						endforeach;
						?>
						<td>
							<?= $total_max; ?>
						</td>
						<td>
							<?= $total_min; ?>
						</td>
						<td>
							<?= $hasil = $total_max-$total_min; ?>
						</td>
					</tr>
					<?php
						$no++;
						$hasil_akhir = [
                            'id_alternatif' => $alternatif->id_alternatif,
                            'nilai' => $hasil,
                        ];
                        
                        DB::table('hasil')->insert($hasil_akhir);
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

@include('layouts.footer_admin')