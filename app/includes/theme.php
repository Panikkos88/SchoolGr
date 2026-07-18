<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>

:root{

--primary:#2563eb;
--success:#16a34a;
--danger:#dc2626;
--warning:#f59e0b;
--dark:#1f2937;
--light:#f4f6f9;
--card:#ffffff;

}

body{

background:var(--light);
font-family:'Inter',sans-serif;
margin:0;
padding:0;

}

.navbar{

background:var(--primary);

}

.navbar-brand{

color:#fff!important;
font-weight:bold;

}

.sidebar{

position:fixed;
left:0;
top:56px;
width:260px;
height:calc(100vh - 56px);

background:#ffffff;

border-right:1px solid #e5e7eb;

overflow-y:auto;

padding:12px;

box-shadow:4px 0 18px rgba(0,0,0,.05);

transition:.3s;

}

.sidebar a{

display:flex;
align-items:center;
gap:12px;

padding:14px 16px;

margin-bottom:6px;

border-radius:12px;

text-decoration:none;

color:#374151;

font-size:15px;

font-weight:500;

transition:.25s;

}

.sidebar a:hover{

background:#2563eb;

color:white;

transform:translateX(5px);

	}
	.sidebar a.active{

background:#2563eb;

color:#fff;

font-weight:600;

	}

.content{

margin-left:250px;
padding:20px;

}

.card-box{

background:#ffffff;

border:1px solid #e5e7eb;

border-radius:18px;

padding:24px;

margin-bottom:20px;

box-shadow:0 8px 24px rgba(15,23,42,.06);

transition:all .25s ease;

}

.card-box:hover{

transform:translateY(-2px);

box-shadow:0 12px 32px rgba(15,23,42,.10);

}

.card-box:hover{

transform:translateY(-2px);

box-shadow:0 12px 32px rgba(15,23,42,.10);

	}

.big-btn{

width:100%;
padding:14px;
font-size:18px;
border-radius:12px;

}

.stat-card{

border-radius:18px;
padding:20px;
text-align:center;
color:#fff;
font-weight:bold;
font-size:20px;
margin-bottom:15px;

}

.primary{

background:#2563eb;

}

.success{

background:#16a34a;

}

.danger{

background:#dc2626;

}

.warning{

background:#f59e0b;
color:#000;

}

.info{

background:#0ea5e9;

}

@media(max-width:992px){

.sidebar{

position:relative;
width:100%;
height:auto;
top:0;

}

.content{

margin-left:0;

}
.btn{

border-radius:12px;

font-weight:600;

transition:.25s;

}

.btn:hover{

transform:translateY(-2px);

	}
}
/* ---------- Buttons ---------- */

.btn{

border-radius:12px;

font-weight:600;

padding:.55rem 1rem;

transition:all .25s ease;

box-shadow:0 2px 6px rgba(0,0,0,.08);

}

.btn:hover{

transform:translateY(-2px);

box-shadow:0 8px 18px rgba(0,0,0,.12);

}

.btn:active{

transform:translateY(0);

	}
	/* =====================================================
   SchoolMedia UI Components
===================================================== */

.sm-card{

background:#fff;

border:1px solid #e5e7eb;

border-radius:20px;

padding:24px;

box-shadow:0 8px 24px rgba(15,23,42,.06);

transition:.25s;

}

.sm-card:hover{

transform:translateY(-3px);

box-shadow:0 14px 30px rgba(15,23,42,.10);

}

.sm-btn{

border-radius:12px;

font-weight:600;

padding:.6rem 1.1rem;

transition:.25s;

}

.sm-btn:hover{

transform:translateY(-2px);

	}
	.dashboard-card{

text-align:center;

height:100%;

}

.dashboard-icon{

font-size:58px;

margin-bottom:12px;

transition:.25s;

}

.dashboard-card:hover .dashboard-icon{

transform:scale(1.12);

	}
	/* ===== SchoolMedia Buttons ===== */

.sm-btn{
    border-radius:14px;
    font-weight:600;
    padding:12px 18px;
    transition:.25s;
    box-shadow:0 4px 12px rgba(0,0,0,.12);
}

.sm-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,.18);
}

.sm-btn i{
    margin-right:8px;
}

.btn-view{
    background:#2563eb;
    color:#fff;
}

.btn-view:hover{
    background:#1d4ed8;
    color:#fff;
}

.btn-edit{
    background:#f59e0b;
    color:#fff;
}

.btn-edit:hover{
    background:#d97706;
    color:#fff;
}

.btn-delete{
    background:#dc2626;
    color:#fff;
}

.btn-delete:hover{
    background:#b91c1c;
    color:#fff;
}

.btn-add{
    background:#16a34a;
    color:#fff;
}

.btn-add:hover{
    background:#15803d;
    color:#fff;
	}
</style>