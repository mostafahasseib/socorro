<?php if (count($top_crashers) > 0) { ?>
<div>
    Top <?php echo count($top_crashers) ?> Crashing Signatures
    <span class="start-date"><?= $start ?></span> through
    <span class="end-date"><?= $last_updated ?></span>.
    The  report covers <span class="percentage" title="<?=$percentTotal?>"><?= number_format($percentTotal * 100, 2)?>%</span> of all <span class="total-num-crashes" title="<?= $total_crashes ?>"><?= number_format($total_crashes) ?></span> crashes during this period.
	<div id="duration-nav">
  	  <h3>Other Periods:</h3>
  	  <ul>
	<?php foreach ($other_durations as $d) { ?>
	    <li><a href="<?= $duration_url . '/' . $d ?>"><?= $d ?> Days</a></li>
	<?php }?>
	</ul></div>
        <table id="signatureList" class="tablesorter">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th title="Movement in Rank since the <?= $start ?> report">Trend</th>
	            <th title="The percentage of crashes against overall crash volume">%</th>
	            <th title="The change in percentage since the <?= $start ?> report">Diff</th>
                    <th>Signature</th>	  
                    <th title="Crashes across platforms">Count</th>
                    <th>Win</th>
                    <th>Mac</th>
                    <th>Lin</th>
           <?php if (isset($sig2bugs)) {?>
               <th class="bugzilla_numbers">Bugzilla Ids</th>
           <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $row = 1 ?>
                <?php foreach ($top_crashers as $crasher): ?>
                    <?php
		      $nonBlankSignature = $crasher->signature ? $crasher->signature : Topcrasher_Controller::$no_sig;
                        $sigParams = array(
                            'range_value' => '2',
                            'range_unit'  => 'weeks',
                            'signature'   => $crasher->signature
			    );
                        if (property_exists($crasher, 'branch')) {
			    $sigParams['branch'] = $crasher->branch;
			} else {
			    $sigParams['version'] = $product . ':' . $version;
			}

                        $link_url =  url::base() . 'report/list?' . html::query_string($sigParams);
                    ?>
                    <tr class="<?php echo ( ($row-1) % 2) == 0 ? 'even' : 'odd' ?>">
                        <td><?php out::H($row) ?></td>
                        <td><div class="trend <?= $crasher->trendClass ?>"><?php echo $crasher->changeInRank ?></div></td>
			 <td><span class="percentOfTotal"><?php out::H($crasher->{'display_percent'}) ?></span></td>
			 <td><div title="A change of <?php out::H($crasher->{'display_change_percent'})?> from <?php out::H($crasher->{'display_previous_percent'}) ?>"
                                ><?php out::H($crasher->{'display_change_percent'}) ?></div></td>
			 <td><a class="signature" href="<?php out::H($link_url) ?>" title="View reports with this crasher."><?php out::H($nonBlankSignature) ?></a><div class="sig-history-graph"></div><div class="sig-history-legend"></div><button name="ajax-signature-<?= $row ?>" value="<?= $nonBlankSignature?>">7 Day Graph</button></td>
                        <td><?php out::H($crasher->count) ?></td>
                        <td><?php out::H($crasher->win_count) ?></td>
                        <td><?php out::H($crasher->mac_count) ?></td>
                        <td><?php out::H($crasher->linux_count) ?></td>


               <?php if (isset($sig2bugs)) {?>
                    <td>
		    <?php if (array_key_exists($crasher->signature, $sig2bugs)) { 
			      $bugs = $sig2bugs[$crasher->signature];
			      for ($i = 0; $i < 3 and $i < count($bugs); $i++) {
				  $bug = $bugs[$i];
				  View::factory('common/bug_number')->set('bug', $bug)->render(TRUE);
				  echo ", ";
			      } ?>
                              <div class="bug_ids_extra">
                        <?php for ($i = 3; $i < count($bugs); $i++) { 
				  $bug = $bugs[$i];
                                  View::factory('common/bug_number')->set('bug', $bug)->render(TRUE);
                              } ?>
			      </div>
			<?php if (count($bugs) > 0) { ?>
			      <a href='#' title="Click to See all likely bug numbers" class="bug_ids_more">More</a>
                            <?php View::factory('common/list_bugs', array(
						      'signature' => $crasher->signature,
						      'bugs' => $bugs,
						      'mode' => 'popup'
				  ))->render(TRUE);
		              }
                         } ?>
                    </td>
               <?php } ?>



                        <?php $row+=1 ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <!--[if IE]><?php echo html::script('js/flot-0.5/excanvas.pack.js') ?><![endif]-->
    <?php echo html::script(array(
        'js/flot-0.5/jquery.flot.pack.js'
    ))?>
    <script type="text/javascript">
    var SocAjax = '<?= url::site('topcrasher/plot_signature') . '/' . $product . '/' . $version . '/' ?>';
    var SocImg = '<?= url::site('img') ?>/';
    </script>
    
    <?php View::factory('common/csv_link_copy')->render(TRUE); ?>
<?php } else { ?>
    <p>No results were found.</p>
<?php } ?>