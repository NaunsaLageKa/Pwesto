@extends('layouts.app')

@section('content')
<div style="min-height:100vh; background:#f8fbfd;">
    <div style="max-width:900px; margin:auto; position:relative;">
        <div style="font-size:2rem;font-weight:bold;color:#19c2b8;letter-spacing:2px;position:absolute;left:0;top:10px;">
            PWESTO
        </div>
        <div style="display:flex;justify-content:center;">
            <div style="width:100%;max-width:540px;margin-top:60px;">
                <div style="background:#fff;padding:2.5rem 2.5rem 2.5rem 2.5rem;border-radius:16px;box-shadow:0 2px 16px #0001;">
                    <div style="text-align:center;margin-bottom:2rem;">
                        <h2 style="font-weight:700;font-size:2rem;">Create your account</h2>
                    </div>
                    <div style="display:flex;gap:0.5rem;margin-bottom:2rem;justify-content:left;">
                        <button type="button" id="userBtn" class="tab-btn" style="padding:0.5rem 1.5rem;border:1.5px solid #1976d2;background:#f8fbfd;color:#1976d2;border-radius:6px;font-weight:500;outline:none;">User</button>
                        <button type="button" id="hubBtn" class="tab-btn" style="padding:0.5rem 1.5rem;border:1.5px solid #e0e0e0;background:#fff;color:#222;border-radius:6px;font-weight:500;outline:none;">Hub Owner</button>
                    </div>
                    <?php if ($errors->any()): ?>
                        <div style="color: red; margin-bottom: 1rem;">
                            <ul>
                                <?php foreach ($errors->all() as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf
                        <input type="hidden" name="role" id="roleInput" value="user">
                        <div id="userFields">
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="name" style="font-weight:500;">Full name</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="email" style="font-weight:500;">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:2rem;text-align:left;">
                                <label for="password" style="font-weight:500;">Password</label>
                                <input id="password" type="password" name="password" required placeholder="Enter your password"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:2rem;text-align:left;">
                                <label for="password_confirmation" style="font-weight:500;">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm your password"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="phone" style="font-weight:500;">Phone Number</label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Enter your phone number"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <button type="submit" style="width:100%;background:#1976d2;color:#fff;padding:1rem 0;border:none;border-radius:10px;font-size:1.1rem;font-weight:600;cursor:pointer;">
                                Sign up
                            </button>
                        </div>
                        <div id="hubFields" style="display:none;">
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="name_hub" style="font-weight:500;">Full Name</label>
                                <input id="name_hub" type="text" name="name" placeholder="Full Name" value="{{ old('name') }}"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="email_hub" style="font-weight:500;">Email</label>
                                <input id="email_hub" type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="company_hub" style="font-weight:500;">Company</label>
                                <select id="company_hub" name="company" required
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                                    <option value="">Select Company</option>
                                    <option value="Produktiv" {{ old('company') == 'Produktiv' ? 'selected' : '' }}>Produktiv</option>
                                    <option value="Nest Workspaces" {{ old('company') == 'Nest Workspaces' ? 'selected' : '' }}>Nest Workspaces</option>
                                    <option value="Mesh Media" {{ old('company') == 'Mesh Media' ? 'selected' : '' }}>Mesh Media</option>
                                </select>
                            </div>
                            <div style="margin-bottom:2rem;text-align:left;display:flex;align-items:center;gap:1rem;">
                                <label for="company_id_hub" style="display:flex;align-items:center;justify-content:center;width:60px;height:60px;background:#f3f6f9;border-radius:10px;cursor:pointer;border:1px solid #e0e0e0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" /></svg>
                                    <input id="company_id_hub" type="file" name="company_id" style="display:none;">
                                </label>
                                <span style="font-size:0.95rem;">Upload Image Company ID Verification</span>
                            </div>
                            <div style="margin-bottom:2rem;text-align:left;">
                                <label for="password_hub" style="font-weight:500;">Password</label>
                                <input id="password_hub" type="password" name="password" placeholder="Password"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:2rem;text-align:left;">
                                <label for="password_confirmation_hub" style="font-weight:500;">Confirm Password</label>
                                <input id="password_confirmation_hub" type="password" name="password_confirmation" placeholder="Confirm Password"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <div style="margin-bottom:1.25rem;text-align:left;">
                                <label for="phone_hub" style="font-weight:500;">Phone Number</label>
                                <input id="phone_hub" type="text" name="phone" placeholder="Phone Number" value="{{ old('phone') }}"
                                    style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;font-size:1.1rem;">
                            </div>
                            <button type="submit" style="width:100%;background:#1976d2;color:#fff;padding:1rem 0;border:none;border-radius:10px;font-size:1.1rem;font-weight:600;cursor:pointer;">
                                Continue to Hub Setup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const userBtn = document.getElementById('userBtn');
    const hubBtn = document.getElementById('hubBtn');
    const userFields = document.getElementById('userFields');
    const hubFields = document.getElementById('hubFields');
    const roleInput = document.getElementById('roleInput');

    function setRequiredAndDisabled(isUser) {
        // User fields
        document.getElementById('name').required = isUser;
        document.getElementById('email').required = isUser;
        document.getElementById('password').required = isUser;
        document.getElementById('password_confirmation').required = isUser;
        document.getElementById('phone').required = isUser;
        document.getElementById('name').disabled = !isUser;
        document.getElementById('email').disabled = !isUser;
        document.getElementById('password').disabled = !isUser;
        document.getElementById('password_confirmation').disabled = !isUser;
        document.getElementById('phone').disabled = !isUser;

        // Hub owner fields
        document.getElementById('name_hub').required = !isUser;
        document.getElementById('email_hub').required = !isUser;
        document.getElementById('password_hub').required = !isUser;
        document.getElementById('password_confirmation_hub').required = !isUser;
        document.getElementById('phone_hub').required = !isUser;
        document.getElementById('company_hub').required = !isUser;
        document.getElementById('name_hub').disabled = isUser;
        document.getElementById('email_hub').disabled = isUser;
        document.getElementById('password_hub').disabled = isUser;
        document.getElementById('password_confirmation_hub').disabled = isUser;
        document.getElementById('phone_hub').disabled = isUser;
        document.getElementById('company_hub').disabled = isUser;
    }

    userBtn.onclick = function() {
        userBtn.style.background = '#f8fbfd';
        userBtn.style.color = '#1976d2';
        userBtn.style.border = '1.5px solid #1976d2';
        hubBtn.style.background = '#fff';
        hubBtn.style.color = '#222';
        hubBtn.style.border = '1.5px solid #e0e0e0';
        userFields.style.display = 'block';
        hubFields.style.display = 'none';
        roleInput.value = 'user';
        setRequiredAndDisabled(true);
    };
    hubBtn.onclick = function() {
        hubBtn.style.background = '#f8fbfd';
        hubBtn.style.color = '#1976d2';
        hubBtn.style.border = '1.5px solid #1976d2';
        userBtn.style.background = '#fff';
        userBtn.style.color = '#222';
        userBtn.style.border = '1.5px solid #e0e0e0';
        userFields.style.display = 'none';
        hubFields.style.display = 'block';
        roleInput.value = 'hub_owner';
        setRequiredAndDisabled(false);
    };
    // Set correct required/disabled fields on page load (default to user)
    setRequiredAndDisabled(true);
</script>
@endsection
