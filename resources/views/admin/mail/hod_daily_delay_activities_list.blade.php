<div>
    
    <div class="form-group">
        <p>Hello <?php echo $name; ?>,</p>
    </div>

    <div class="form-group">
        <p>
            <b>
                Below are the list of
            </b> 
            <b style="color: blue;"> 
                current week Planned 
            </b>
            <b> 
                & 
            </b>
            <b style="color: red;">
                till now Delay
            </b>
            <b>
                activities, kindly take necessary action.
            </b>
        </p>
    </div>

    @if(count($upcomingActivities)>0)
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: blue; column-span:7;">
                    List of current week planned activities
                </th>
            </tr>
            <tr style="border: 1px solid;">
                <center>
                    <th style="border: 1px solid;">Sr.No</th>
                    <th style="border: 1px solid;">Study No</th>
                    <th style="border: 1px solid;">Activity Name</th>
                    <th style="border: 1px solid;">Schedule Start Date</th>
                    <th style="border: 1px solid;">Schedule End Date</th>
                    @if($role == 11)
                        <th style="border: 1px solid;">CR location</th>
                    @else
                        <th style="border: 1px solid;">BR location</th>
                    @endif
                    <th style="border: 1px solid;">User Name</th>
                </center>
            </tr>
        
            @if(!is_null($upcomingActivities))
                @foreach($upcomingActivities as $uak => $uav)
                    <center>
                        <tr style="border: 1px solid;">
                            <td style="border: 1px solid;"><?php echo $loop->iteration; ?></td>
                            <td style="border: 1px solid;"><?php echo $uav->studyNo['study_no']; ?></td>
                            <td style="border: 1px solid;"><?php echo $uav['activity_name']; ?></td>
                            <td style="border: 1px solid;"><?php echo date('d M Y', strtotime($uav['scheduled_start_date'])); ?></td>
                            <td style="border: 1px solid;"><?php echo date('d M Y', strtotime($uav['scheduled_end_date'])); ?></td>
                            @if($role == 11)
                                <td style="border: 1px solid;"><?php echo $uav->studyNo->crLocationName['location_name']; ?></td>
                            @else
                                <td style="border: 1px solid;"><?php echo $uav->studyNo->brLocationName['location_name']; ?></td>
                            @endif
                            @if(!is_null($uav->userName))
                                @php $name = []; @endphp
                                @foreach($uav->userName as $rk => $rv)
                                @if($uav->studyNo->crLocationName->id == $rv->location_id || $uav->studyNo->brLocationName->id == $rv->location_id)
                                    @php 
                                        $name[] = $rv->name;
                                    @endphp
                                @endif
                                @endforeach
                                <td style="border: 1px solid;">
                                    <?php echo implode(' | ', $name);  ?>
                                </td>
                            @endif
                        </tr>
                    </center>
                @endforeach
            @endif
            
        </table>
        <br>
        <br>
    @else
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: blue;">
                    No planned activities for this week
                </th>
            </tr>
        </table>
    @endif<br><br>

    @if(count($delayActivities)>0)
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: red; column-span:7;">
                    List of delay activities
                </th>
            </tr>
            <tr style="border: 1px solid;">
                <center>
                    <th style="border: 1px solid;">Sr.No</th>
                    <th style="border: 1px solid;">Study No</th>
                    <th style="border: 1px solid;">Activity Name</th>
                    <th style="border: 1px solid;">Schedule Start Date</th>
                    <th style="border: 1px solid;">Schedule End Date</th>
                    @if($role == 11)
                        <th style="border: 1px solid;">CR location</th>
                    @else
                        <th style="border: 1px solid;">BR location</th>
                    @endif
                    <th style="border: 1px solid;">User Name</th>
                </center>
            </tr>
        
            @if(!is_null($delayActivities))
                @foreach($delayActivities as $dak => $dav)
                    <center>
                        <tr style="border: 1px solid;">
                            <td style="border: 1px solid;"><?php echo $loop->iteration; ?></td>
                            <td style="border: 1px solid;"><?php echo $dav->studyNo['study_no']; ?></td>
                            <td style="border: 1px solid;"><?php echo $dav['activity_name']; ?></td>
                            <td style="border: 1px solid;"><?php echo date('d M Y', strtotime($dav['scheduled_start_date'])); ?></td>
                            <td style="border: 1px solid;"><?php echo date('d M Y', strtotime($dav['scheduled_end_date'])); ?></td>
                            @if($role == 11)
                                <td style="border: 1px solid;"><?php echo $dav->studyNo->crLocationName['location_name']; ?></td>
                            @else
                                <td style="border: 1px solid;"><?php echo $dav->studyNo->brLocationName['location_name']; ?></td>
                            @endif
                            @if(!is_null($dav->userName))
                                @php $name = []; @endphp
                                @foreach($dav->userName as $ulk => $ulv)
                                @if($dav->studyNo->crLocationName->id == $ulv->location_id || $dav->studyNo->brLocationName->id == $ulv->location_id)
                                    @php 
                                        $name[] = $ulv->name;
                                    @endphp
                                @endif
                                @endforeach
                                <td style="border: 1px solid;">
                                    <?php echo implode(' | ', $name);  ?>
                                </td>
                            @endif
                        </tr>
                    </center>
                @endforeach
            @endif
            
        </table>
    @else 
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: red;">
                    No delay activities
                </th>
            </tr>
        </table>
    @endif

    <p>
        <b>Note:</b> Please do not reply to this email, this is system generated email from Study Management System.
    </p>
    
    <div class="form-group">
        <h4>Study Management System</h4>
    </div>
        
</div>
