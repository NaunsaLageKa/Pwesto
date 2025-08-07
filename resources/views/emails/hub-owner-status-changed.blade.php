<div style="font-family: Arial, sans-serif;">
    <h2>Hello {{ $owner->name }},</h2>
    @if($owner->status === 'approved')
        <p>Congratulations! Your hub owner registration has been <strong>approved</strong>. You can now manage your coworking space and accept bookings from users.</p>
    @elseif($owner->status === 'rejected')
        <p>We regret to inform you that your hub owner registration has been <strong>rejected</strong>. If you have questions, please contact support.</p>
    @endif
    <p>Thank you,<br>PWESTO Team</p>
</div> x`x