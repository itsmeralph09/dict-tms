<?php
require './function/encrypt_decrypt.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php include './include/head.php'; ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include './include/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include './include/topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Events</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Full Calendar -->
            <div class="col-xl-12 col-lg-8">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div
                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">List of Events</h6>
                         <div>
                            <a class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#addnew"><i class="fas fa-plus fa-sm text-white-50"></i> Add event</a>
                            <a href="deleted_events.php" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-trash fa-sm text-white-50"></i> Archived Events</a>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-striped" id="myTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                      
                                        <th scope="col">Event Name</th>                                        
                                        <th scope="col">Event Date</th>                                        
                                        <th scope="col">Host Office</th>                                        
                                        <th scope="col">Speaker Name</th>                             
                                        <th scope="col">Participants</th>                             
                                        <th scope="col">Status</th>                             
                                        <th scope="col">Action</th>                             
                                       
                                    </tr>
                                </thead>
                                
                                <tbody>

                                    <?php

                                    require '../db/dbconn.php';
                                    $display_events = "
                                                        SELECT et.*, CONCAT(st.first_name,' ',st.last_name) as speaker_name, ht.office
                                                        FROM events et
                                                        INNER JOIN speakers st ON st.speaker_id = et.speaker_id
                                                        INNER JOIN host_office ht ON ht.host_id = et.host_id
                                                        WHERE et.deleted != 1
                                                    ";
                                    $sqlQuery = mysqli_query($con, $display_events) or die( mysqli_error($con));
                                    
                                    while($row = mysqli_fetch_array($sqlQuery)){
                                        $event_id = $row['event_id'];
                                        $event_name = $row['event_name'];
                                        $event_description = $row['event_description']; 
                                        $host_office = $row['office']; 
                                        $host_id = $row['host_id']; 
                                        $speaker_id = $row['speaker_id'];                                 
                                        $event_speaker = $row['speaker_name'];                 
                                        $status = $row['status'];
                                        $start_date = $row['event_start'];
                                        $end_date = $row['event_end'];

                                        $count_participants = "SELECT COUNT(participant_id) as participants_count FROM `participants` WHERE event_id = '$event_id'";
                                        $sqlQueryCount = mysqli_query($con, $count_participants) or die( mysqli_error($con));
                                        $rowCount = mysqli_fetch_array($sqlQueryCount);
                                        $participants_count = $rowCount['participants_count'];
                                        
                                        if ($status == "closed") {
                                            $status_button = "<a class='btn btn-sm btn-success start-event-btn shadow-sm'
                                                                data-toggle='tooltip' data-placement='right' title='Open ".$event_name."'
                                                                data-event-id=".$event_id."
                                                                data-event-name=".htmlspecialchars($event_name)."
                                                                data-event-speaker=".htmlspecialchars($event_speaker)."><i class='fa-solid fa-play'></i>
                                                              </a>";
                                        } elseif ($status == "started") {
                                            $status_button = "<a class='btn btn-sm btn-secondary stop-event-btn shadow-sm'
                                                                data-toggle='tooltip' data-placement='right' title='Close ".$event_name."'
                                                                data-event-id=".$event_id."
                                                                data-event-name=".htmlspecialchars($event_name)."
                                                                data-event-speaker=".htmlspecialchars($event_speaker)."><i class='fa-solid fa-stop'></i>
                                                              </a>";
                                        }
                                        if ($status == "closed") {
                                            $status_badge = "<div class='row'><div class='col text-center'><i class='fa-solid fa-circle-xmark fa-xl'></i></div></div>";
                                        } elseif ($status == "started") {
                                            $status_badge = "<div class='row'><div class='col text-center'><i class='fa-solid fa-circle-check text-success fa-xl'></i></div></div>";
                                        }
                                    ?>
                                    <tr>                                          
                                        <td class=""><?php echo $event_name; ?></td>
                                        <td class=""><?php echo date('F d, Y', strtotime($start_date)); ?></td>
                                        <td><?php echo $host_office; ?></td>
                                        <td><?php echo $event_speaker; ?></td>
                                        <td class="text-center"><?php echo $participants_count; ?> participants <a href="participants.php?event_id=<?php echo urlencode($event_id); ?>&event_name=<?php echo urlencode($event_name); ?>" data-toggle="tooltip" data-placement="right" title="View <?php echo $event_name; ?> Participants">(view)</a></td>
                                        <td><?php echo $status_badge; ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#edit_<?php echo $event_id; ?>" data-toggle="tooltip" data-placement="right" title="Edit <?php echo $event_name; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <?php echo $status_button; ?>
                                            <a href="#" class="btn btn-sm btn-danger delete-event-btn shadow-sm"
                                               data-toggle="tooltip" data-placement="right" title="Delete <?php echo $event_name; ?>"
                                               data-event-id="<?php echo $event_id; ?>" 
                                               data-event-name="<?php echo htmlspecialchars($event_name); ?>" 
                                               data-event-speaker="<?php echo htmlspecialchars($event_speaker); ?>"><i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        include('modal/event_edit_modal.php');
                                        } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('modal/event_add_modal.php'); ?>     
        </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <?php include './include/logout_modal.php'; ?>

    <?php include './include/script.php'; ?>

<script>
    $(document).ready(function(){
    //inialize datatable
    $('#myTable').DataTable({
        scrollX: true,
    })

    });

</script>

<script>
    $(document).ready(function() {
        // Function for starting event
        $('.start-event-btn').on('click', function(e) {
            e.preventDefault();
            var startBtn = $(this);
            var eventId = startBtn.data('event-id');

            Swal.fire({
                title: 'Open Event',
                text: "Are you sure you want to open this event?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, open it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'action/start_event.php', // Adjust the URL to your PHP script for starting the event
                        type: 'POST',
                        data: {
                            event_id: eventId
                        },
                        success: function(response) {
                            if (response.trim() === 'success') {
                                Swal.fire(
                                    'Event Opened!',
                                    'The event has been opened.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to open event.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Failed to open event.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Function for stopping event
        $('.stop-event-btn').on('click', function(e) {
            e.preventDefault();
            var stopBtn = $(this);
            var eventId = stopBtn.data('event-id');

            Swal.fire({
                title: 'Close Event',
                text: "Are you sure you want to close this event?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'action/stop_event.php', // Adjust the URL to your PHP script for stopping the event
                        type: 'POST',
                        data: {
                            event_id: eventId
                        },
                        success: function(response) {
                            if (response.trim() === 'success') {
                                Swal.fire(
                                    'Event Stopped!',
                                    'The event has been closed.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to close event.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Failed to close event.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Function for deleting event
        $('.delete-event-btn').on('click', function(e) {
            e.preventDefault();
            var deleteBtn = $(this);
            var eventId = deleteBtn.data('event-id');
            var eventName = decodeURIComponent(deleteBtn.data('event-name'));
            var eventSpeaker = decodeURIComponent(deleteBtn.data('event-speaker'));

            Swal.fire({
                title: 'Delete Event',
                html: "You are about to delete the following event:<br><br>" +
                      "<strong>Event Name:</strong> " + eventName + "<br>" +
                      "<strong>Event Speaker:</strong> " + eventSpeaker + "<br>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'action/delete_event.php', // Adjust the URL to your PHP script for deleting the event
                        type: 'POST',
                        data: {
                            event_id: eventId
                        },
                        success: function(response) {
                            if (response.trim() === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    'Event has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete event.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Failed to delete event.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#addEvent').on('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            var formData = $('#addnew form').serialize(); // Serialize form data

            $.ajax({
                url: 'action/add_event.php', // URL to submit the form data
                type: 'POST',
                data: formData, // Form data to be submitted
                success: function(response) {
                    // Handle the success response
                    console.log(response); // Output response to console (for debugging)
                    Swal.fire({
                        icon: 'success',
                        title: 'Event added successfully',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });   
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error(xhr.responseText); // Output error response to console (for debugging)
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to add event',
                        text: 'Please try again later.',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    }); 
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // For dynamically rendered modals
        $(document).on('click', '[id^="updateEvent_"]', function(e) {
            e.preventDefault(); // Prevent default form submission
            var eventId = $(this).attr('id').split('_')[1]; // Extract event ID
            var formData = $('#edit_' + eventId + ' form').serialize(); // Serialize form data

            $.ajax({
                url: 'action/update_event.php', // URL to submit the form data
                type: 'POST',
                data: formData, // Form data to be submitted
                success: function(response) {
                    // Handle the success response
                    console.log(response); // Output response to console (for debugging)
                    Swal.fire({
                        icon: 'success',
                        title: 'Event updated successfully',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });   
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error(xhr.responseText); // Output error response to console (for debugging)
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to update event',
                        text: 'Please try again later.',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    }); 
                }
            });
        });
    });
</script>



</body>

</html>