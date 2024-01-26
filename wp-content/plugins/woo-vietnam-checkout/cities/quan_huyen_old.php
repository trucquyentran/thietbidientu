<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$quan_huyen = array(
	"0" => array("maqh"=>"001","name"=>"Quận Ba Đình","matp"=>"01"),
	"1" => array("maqh"=>"002","name"=>"Quận Hoàn Kiếm","matp"=>"01"),
	"2" => array("maqh"=>"003","name"=>"Quận Tây Hồ","matp"=>"01"),
	"3" => array("maqh"=>"004","name"=>"Quận Long Biên","matp"=>"01"),
	"4" => array("maqh"=>"005","name"=>"Quận Cầu Giấy","matp"=>"01"),
	"5" => array("maqh"=>"006","name"=>"Quận Đống Đa","matp"=>"01"),
	"6" => array("maqh"=>"007","name"=>"Quận Hai Bà Trưng","matp"=>"01"),
	"7" => array("maqh"=>"008","name"=>"Quận Hoàng Mai","matp"=>"01"),
	"8" => array("maqh"=>"009","name"=>"Quận Thanh Xuân","matp"=>"01"),
	"9" => array("maqh"=>"016","name"=>"Huyện Sóc Sơn","matp"=>"01"),
	"10" => array("maqh"=>"017","name"=>"Huyện Đông Anh","matp"=>"01"),
	"11" => array("maqh"=>"018","name"=>"Huyện Gia Lâm","matp"=>"01"),
	"12" => array("maqh"=>"019","name"=>"Quận Nam Từ Liêm","matp"=>"01"),
	"13" => array("maqh"=>"020","name"=>"Huyện Thanh Trì","matp"=>"01"),
	"14" => array("maqh"=>"021","name"=>"Quận Bắc Từ Liêm","matp"=>"01"),
	"15" => array("maqh"=>"024","name"=>"Thành phố Hà Giang","matp"=>"02"),
	"16" => array("maqh"=>"026","name"=>"Huyện Đồng Văn","matp"=>"02"),
	"17" => array("maqh"=>"027","name"=>"Huyện Mèo Vạc","matp"=>"02"),
	"18" => array("maqh"=>"028","name"=>"Huyện Yên Minh","matp"=>"02"),
	"19" => array("maqh"=>"029","name"=>"Huyện Quản Bạ","matp"=>"02"),
	"20" => array("maqh"=>"030","name"=>"Huyện Vị Xuyên","matp"=>"02"),
	"21" => array("maqh"=>"031","name"=>"Huyện Bắc Mê","matp"=>"02"),
	"22" => array("maqh"=>"032","name"=>"Huyện Hoàng Su Phì","matp"=>"02"),
	"23" => array("maqh"=>"033","name"=>"Huyện Xín Mần","matp"=>"02"),
	"24" => array("maqh"=>"034","name"=>"Huyện Bắc Quang","matp"=>"02"),
	"25" => array("maqh"=>"035","name"=>"Huyện Quang Bình","matp"=>"02"),
	"26" => array("maqh"=>"040","name"=>"Thành phố Cao Bằng","matp"=>"04"),
	"27" => array("maqh"=>"042","name"=>"Huyện Bảo Lâm","matp"=>"04"),
	"28" => array("maqh"=>"043","name"=>"Huyện Bảo Lạc","matp"=>"04"),
	"29" => array("maqh"=>"044","name"=>"Huyện Thông Nông","matp"=>"04"),
	"30" => array("maqh"=>"045","name"=>"Huyện Hà Quảng","matp"=>"04"),
	"31" => array("maqh"=>"046","name"=>"Huyện Trà Lĩnh","matp"=>"04"),
	"32" => array("maqh"=>"047","name"=>"Huyện Trùng Khánh","matp"=>"04"),
	"33" => array("maqh"=>"048","name"=>"Huyện Hạ Lang","matp"=>"04"),
	"34" => array("maqh"=>"049","name"=>"Huyện Quảng Uyên","matp"=>"04"),
	"35" => array("maqh"=>"050","name"=>"Huyện Phục Hoà","matp"=>"04"),
	"36" => array("maqh"=>"051","name"=>"Huyện Hoà An","matp"=>"04"),
	"37" => array("maqh"=>"052","name"=>"Huyện Nguyên Bình","matp"=>"04"),
	"38" => array("maqh"=>"053","name"=>"Huyện Thạch An","matp"=>"04"),
	"39" => array("maqh"=>"058","name"=>"Thành Phố Bắc Kạn","matp"=>"06"),
	"40" => array("maqh"=>"060","name"=>"Huyện Pác Nặm","matp"=>"06"),
	"41" => array("maqh"=>"061","name"=>"Huyện Ba Bể","matp"=>"06"),
	"42" => array("maqh"=>"062","name"=>"Huyện Ngân Sơn","matp"=>"06"),
	"43" => array("maqh"=>"063","name"=>"Huyện Bạch Thông","matp"=>"06"),
	"44" => array("maqh"=>"064","name"=>"Huyện Chợ Đồn","matp"=>"06"),
	"45" => array("maqh"=>"065","name"=>"Huyện Chợ Mới","matp"=>"06"),
	"46" => array("maqh"=>"066","name"=>"Huyện Na Rì","matp"=>"06"),
	"47" => array("maqh"=>"070","name"=>"Thành phố Tuyên Quang","matp"=>"08"),
	"48" => array("maqh"=>"071","name"=>"Huyện Lâm Bình","matp"=>"08"),
	"49" => array("maqh"=>"072","name"=>"Huyện Nà Hang","matp"=>"08"),
	"50" => array("maqh"=>"073","name"=>"Huyện Chiêm Hóa","matp"=>"08"),
	"51" => array("maqh"=>"074","name"=>"Huyện Hàm Yên","matp"=>"08"),
	"52" => array("maqh"=>"075","name"=>"Huyện Yên Sơn","matp"=>"08"),
	"53" => array("maqh"=>"076","name"=>"Huyện Sơn Dương","matp"=>"08"),
	"54" => array("maqh"=>"080","name"=>"Thành phố Lào Cai","matp"=>"10"),
	"55" => array("maqh"=>"082","name"=>"Huyện Bát Xát","matp"=>"10"),
	"56" => array("maqh"=>"083","name"=>"Huyện Mường Khương","matp"=>"10"),
	"57" => array("maqh"=>"084","name"=>"Huyện Si Ma Cai","matp"=>"10"),
	"58" => array("maqh"=>"085","name"=>"Huyện Bắc Hà","matp"=>"10"),
	"59" => array("maqh"=>"086","name"=>"Huyện Bảo Thắng","matp"=>"10"),
	"60" => array("maqh"=>"087","name"=>"Huyện Bảo Yên","matp"=>"10"),
	"61" => array("maqh"=>"088","name"=>"Huyện Sa Pa","matp"=>"10"),
	"62" => array("maqh"=>"089","name"=>"Huyện Văn Bàn","matp"=>"10"),
	"63" => array("maqh"=>"094","name"=>"Thành phố Điện Biên Phủ","matp"=>"11"),
	"64" => array("maqh"=>"095","name"=>"Thị Xã Mường Lay","matp"=>"11"),
	"65" => array("maqh"=>"096","name"=>"Huyện Mường Nhé","matp"=>"11"),
	"66" => array("maqh"=>"097","name"=>"Huyện Mường Chà","matp"=>"11"),
	"67" => array("maqh"=>"098","name"=>"Huyện Tủa Chùa","matp"=>"11"),
	"68" => array("maqh"=>"099","name"=>"Huyện Tuần Giáo","matp"=>"11"),
	"69" => array("maqh"=>"100","name"=>"Huyện Điện Biên","matp"=>"11"),
	"70" => array("maqh"=>"101","name"=>"Huyện Điện Biên Đông","matp"=>"11"),
	"71" => array("maqh"=>"102","name"=>"Huyện Mường Ảng","matp"=>"11"),
	"72" => array("maqh"=>"103","name"=>"Huyện Nậm Pồ","matp"=>"11"),
	"73" => array("maqh"=>"105","name"=>"Thành phố Lai Châu","matp"=>"12"),
	"74" => array("maqh"=>"106","name"=>"Huyện Tam Đường","matp"=>"12"),
	"75" => array("maqh"=>"107","name"=>"Huyện Mường Tè","matp"=>"12"),
	"76" => array("maqh"=>"108","name"=>"Huyện Sìn Hồ","matp"=>"12"),
	"77" => array("maqh"=>"109","name"=>"Huyện Phong Thổ","matp"=>"12"),
	"78" => array("maqh"=>"110","name"=>"Huyện Than Uyên","matp"=>"12"),
	"79" => array("maqh"=>"111","name"=>"Huyện Tân Uyên","matp"=>"12"),
	"80" => array("maqh"=>"112","name"=>"Huyện Nậm Nhùn","matp"=>"12"),
	"81" => array("maqh"=>"116","name"=>"Thành phố Sơn La","matp"=>"14"),
	"82" => array("maqh"=>"118","name"=>"Huyện Quỳnh Nhai","matp"=>"14"),
	"83" => array("maqh"=>"119","name"=>"Huyện Thuận Châu","matp"=>"14"),
	"84" => array("maqh"=>"120","name"=>"Huyện Mường La","matp"=>"14"),
	"85" => array("maqh"=>"121","name"=>"Huyện Bắc Yên","matp"=>"14"),
	"86" => array("maqh"=>"122","name"=>"Huyện Phù Yên","matp"=>"14"),
	"87" => array("maqh"=>"123","name"=>"Huyện Mộc Châu","matp"=>"14"),
	"88" => array("maqh"=>"124","name"=>"Huyện Yên Châu","matp"=>"14"),
	"89" => array("maqh"=>"125","name"=>"Huyện Mai Sơn","matp"=>"14"),
	"90" => array("maqh"=>"126","name"=>"Huyện Sông Mã","matp"=>"14"),
	"91" => array("maqh"=>"127","name"=>"Huyện Sốp Cộp","matp"=>"14"),
	"92" => array("maqh"=>"128","name"=>"Huyện Vân Hồ","matp"=>"14"),
	"93" => array("maqh"=>"132","name"=>"Thành phố Yên Bái","matp"=>"15"),
	"94" => array("maqh"=>"133","name"=>"Thị xã Nghĩa Lộ","matp"=>"15"),
	"95" => array("maqh"=>"135","name"=>"Huyện Lục Yên","matp"=>"15"),
	"96" => array("maqh"=>"136","name"=>"Huyện Văn Yên","matp"=>"15"),
	"97" => array("maqh"=>"137","name"=>"Huyện Mù Căng Chải","matp"=>"15"),
	"98" => array("maqh"=>"138","name"=>"Huyện Trấn Yên","matp"=>"15"),
	"99" => array("maqh"=>"139","name"=>"Huyện Trạm Tấu","matp"=>"15"),
	"100" => array("maqh"=>"140","name"=>"Huyện Văn Chấn","matp"=>"15"),
	"101" => array("maqh"=>"141","name"=>"Huyện Yên Bình","matp"=>"15"),
	"102" => array("maqh"=>"148","name"=>"Thành phố Hòa Bình","matp"=>"17"),
	"103" => array("maqh"=>"150","name"=>"Huyện Đà Bắc","matp"=>"17"),
	"104" => array("maqh"=>"151","name"=>"Huyện Kỳ Sơn","matp"=>"17"),
	"105" => array("maqh"=>"152","name"=>"Huyện Lương Sơn","matp"=>"17"),
	"106" => array("maqh"=>"153","name"=>"Huyện Kim Bôi","matp"=>"17"),
	"107" => array("maqh"=>"154","name"=>"Huyện Cao Phong","matp"=>"17"),
	"108" => array("maqh"=>"155","name"=>"Huyện Tân Lạc","matp"=>"17"),
	"109" => array("maqh"=>"156","name"=>"Huyện Mai Châu","matp"=>"17"),
	"110" => array("maqh"=>"157","name"=>"Huyện Lạc Sơn","matp"=>"17"),
	"111" => array("maqh"=>"158","name"=>"Huyện Yên Thủy","matp"=>"17"),
	"112" => array("maqh"=>"159","name"=>"Huyện Lạc Thủy","matp"=>"17"),
	"113" => array("maqh"=>"164","name"=>"Thành phố Thái Nguyên","matp"=>"19"),
	"114" => array("maqh"=>"165","name"=>"Thành phố Sông Công","matp"=>"19"),
	"115" => array("maqh"=>"167","name"=>"Huyện Định Hóa","matp"=>"19"),
	"116" => array("maqh"=>"168","name"=>"Huyện Phú Lương","matp"=>"19"),
	"117" => array("maqh"=>"169","name"=>"Huyện Đồng Hỷ","matp"=>"19"),
	"118" => array("maqh"=>"170","name"=>"Huyện Võ Nhai","matp"=>"19"),
	"119" => array("maqh"=>"171","name"=>"Huyện Đại Từ","matp"=>"19"),
	"120" => array("maqh"=>"172","name"=>"Thị xã Phổ Yên","matp"=>"19"),
	"121" => array("maqh"=>"173","name"=>"Huyện Phú Bình","matp"=>"19"),
	"122" => array("maqh"=>"178","name"=>"Thành phố Lạng Sơn","matp"=>"20"),
	"123" => array("maqh"=>"180","name"=>"Huyện Tràng Định","matp"=>"20"),
	"124" => array("maqh"=>"181","name"=>"Huyện Bình Gia","matp"=>"20"),
	"125" => array("maqh"=>"182","name"=>"Huyện Văn Lãng","matp"=>"20"),
	"126" => array("maqh"=>"183","name"=>"Huyện Cao Lộc","matp"=>"20"),
	"127" => array("maqh"=>"184","name"=>"Huyện Văn Quan","matp"=>"20"),
	"128" => array("maqh"=>"185","name"=>"Huyện Bắc Sơn","matp"=>"20"),
	"129" => array("maqh"=>"186","name"=>"Huyện Hữu Lũng","matp"=>"20"),
	"130" => array("maqh"=>"187","name"=>"Huyện Chi Lăng","matp"=>"20"),
	"131" => array("maqh"=>"188","name"=>"Huyện Lộc Bình","matp"=>"20"),
	"132" => array("maqh"=>"189","name"=>"Huyện Đình Lập","matp"=>"20"),
	"133" => array("maqh"=>"193","name"=>"Thành phố Hạ Long","matp"=>"22"),
	"134" => array("maqh"=>"194","name"=>"Thành phố Móng Cái","matp"=>"22"),
	"135" => array("maqh"=>"195","name"=>"Thành phố Cẩm Phả","matp"=>"22"),
	"136" => array("maqh"=>"196","name"=>"Thành phố Uông Bí","matp"=>"22"),
	"137" => array("maqh"=>"198","name"=>"Huyện Bình Liêu","matp"=>"22"),
	"138" => array("maqh"=>"199","name"=>"Huyện Tiên Yên","matp"=>"22"),
	"139" => array("maqh"=>"200","name"=>"Huyện Đầm Hà","matp"=>"22"),
	"140" => array("maqh"=>"201","name"=>"Huyện Hải Hà","matp"=>"22"),
	"141" => array("maqh"=>"202","name"=>"Huyện Ba Chẽ","matp"=>"22"),
	"142" => array("maqh"=>"203","name"=>"Huyện Vân Đồn","matp"=>"22"),
	"143" => array("maqh"=>"204","name"=>"Huyện Hoành Bồ","matp"=>"22"),
	"144" => array("maqh"=>"205","name"=>"Thị xã Đông Triều","matp"=>"22"),
	"145" => array("maqh"=>"206","name"=>"Thị xã Quảng Yên","matp"=>"22"),
	"146" => array("maqh"=>"207","name"=>"Huyện Cô Tô","matp"=>"22"),
	"147" => array("maqh"=>"213","name"=>"Thành phố Bắc Giang","matp"=>"24"),
	"148" => array("maqh"=>"215","name"=>"Huyện Yên Thế","matp"=>"24"),
	"149" => array("maqh"=>"216","name"=>"Huyện Tân Yên","matp"=>"24"),
	"150" => array("maqh"=>"217","name"=>"Huyện Lạng Giang","matp"=>"24"),
	"151" => array("maqh"=>"218","name"=>"Huyện Lục Nam","matp"=>"24"),
	"152" => array("maqh"=>"219","name"=>"Huyện Lục Ngạn","matp"=>"24"),
	"153" => array("maqh"=>"220","name"=>"Huyện Sơn Động","matp"=>"24"),
	"154" => array("maqh"=>"221","name"=>"Huyện Yên Dũng","matp"=>"24"),
	"155" => array("maqh"=>"222","name"=>"Huyện Việt Yên","matp"=>"24"),
	"156" => array("maqh"=>"223","name"=>"Huyện Hiệp Hòa","matp"=>"24"),
	"157" => array("maqh"=>"227","name"=>"Thành phố Việt Trì","matp"=>"25"),
	"158" => array("maqh"=>"228","name"=>"Thị xã Phú Thọ","matp"=>"25"),
	"159" => array("maqh"=>"230","name"=>"Huyện Đoan Hùng","matp"=>"25"),
	"160" => array("maqh"=>"231","name"=>"Huyện Hạ Hoà","matp"=>"25"),
	"161" => array("maqh"=>"232","name"=>"Huyện Thanh Ba","matp"=>"25"),
	"162" => array("maqh"=>"233","name"=>"Huyện Phù Ninh","matp"=>"25"),
	"163" => array("maqh"=>"234","name"=>"Huyện Yên Lập","matp"=>"25"),
	"164" => array("maqh"=>"235","name"=>"Huyện Cẩm Khê","matp"=>"25"),
	"165" => array("maqh"=>"236","name"=>"Huyện Tam Nông","matp"=>"25"),
	"166" => array("maqh"=>"237","name"=>"Huyện Lâm Thao","matp"=>"25"),
	"167" => array("maqh"=>"238","name"=>"Huyện Thanh Sơn","matp"=>"25"),
	"168" => array("maqh"=>"239","name"=>"Huyện Thanh Thuỷ","matp"=>"25"),
	"169" => array("maqh"=>"240","name"=>"Huyện Tân Sơn","matp"=>"25"),
	"170" => array("maqh"=>"243","name"=>"Thành phố Vĩnh Yên","matp"=>"26"),
	"171" => array("maqh"=>"244","name"=>"Thị xã Phúc Yên","matp"=>"26"),
	"172" => array("maqh"=>"246","name"=>"Huyện Lập Thạch","matp"=>"26"),
	"173" => array("maqh"=>"247","name"=>"Huyện Tam Dương","matp"=>"26"),
	"174" => array("maqh"=>"248","name"=>"Huyện Tam Đảo","matp"=>"26"),
	"175" => array("maqh"=>"249","name"=>"Huyện Bình Xuyên","matp"=>"26"),
	"176" => array("maqh"=>"250","name"=>"Huyện Mê Linh","matp"=>"01"),
	"177" => array("maqh"=>"251","name"=>"Huyện Yên Lạc","matp"=>"26"),
	"178" => array("maqh"=>"252","name"=>"Huyện Vĩnh Tường","matp"=>"26"),
	"179" => array("maqh"=>"253","name"=>"Huyện Sông Lô","matp"=>"26"),
	"180" => array("maqh"=>"256","name"=>"Thành phố Bắc Ninh","matp"=>"27"),
	"181" => array("maqh"=>"258","name"=>"Huyện Yên Phong","matp"=>"27"),
	"182" => array("maqh"=>"259","name"=>"Huyện Quế Võ","matp"=>"27"),
	"183" => array("maqh"=>"260","name"=>"Huyện Tiên Du","matp"=>"27"),
	"184" => array("maqh"=>"261","name"=>"Thị xã Từ Sơn","matp"=>"27"),
	"185" => array("maqh"=>"262","name"=>"Huyện Thuận Thành","matp"=>"27"),
	"186" => array("maqh"=>"263","name"=>"Huyện Gia Bình","matp"=>"27"),
	"187" => array("maqh"=>"264","name"=>"Huyện Lương Tài","matp"=>"27"),
	"188" => array("maqh"=>"268","name"=>"Quận Hà Đông","matp"=>"01"),
	"189" => array("maqh"=>"269","name"=>"Thị xã Sơn Tây","matp"=>"01"),
	"190" => array("maqh"=>"271","name"=>"Huyện Ba Vì","matp"=>"01"),
	"191" => array("maqh"=>"272","name"=>"Huyện Phúc Thọ","matp"=>"01"),
	"192" => array("maqh"=>"273","name"=>"Huyện Đan Phượng","matp"=>"01"),
	"193" => array("maqh"=>"274","name"=>"Huyện Hoài Đức","matp"=>"01"),
	"194" => array("maqh"=>"275","name"=>"Huyện Quốc Oai","matp"=>"01"),
	"195" => array("maqh"=>"276","name"=>"Huyện Thạch Thất","matp"=>"01"),
	"196" => array("maqh"=>"277","name"=>"Huyện Chương Mỹ","matp"=>"01"),
	"197" => array("maqh"=>"278","name"=>"Huyện Thanh Oai","matp"=>"01"),
	"198" => array("maqh"=>"279","name"=>"Huyện Thường Tín","matp"=>"01"),
	"199" => array("maqh"=>"280","name"=>"Huyện Phú Xuyên","matp"=>"01"),
	"200" => array("maqh"=>"281","name"=>"Huyện Ứng Hòa","matp"=>"01"),
	"201" => array("maqh"=>"282","name"=>"Huyện Mỹ Đức","matp"=>"01"),
	"202" => array("maqh"=>"288","name"=>"Thành phố Hải Dương","matp"=>"30"),
	"203" => array("maqh"=>"290","name"=>"Thị xã Chí Linh","matp"=>"30"),
	"204" => array("maqh"=>"291","name"=>"Huyện Nam Sách","matp"=>"30"),
	"205" => array("maqh"=>"292","name"=>"Huyện Kinh Môn","matp"=>"30"),
	"206" => array("maqh"=>"293","name"=>"Huyện Kim Thành","matp"=>"30"),
	"207" => array("maqh"=>"294","name"=>"Huyện Thanh Hà","matp"=>"30"),
	"208" => array("maqh"=>"295","name"=>"Huyện Cẩm Giàng","matp"=>"30"),
	"209" => array("maqh"=>"296","name"=>"Huyện Bình Giang","matp"=>"30"),
	"210" => array("maqh"=>"297","name"=>"Huyện Gia Lộc","matp"=>"30"),
	"211" => array("maqh"=>"298","name"=>"Huyện Tứ Kỳ","matp"=>"30"),
	"212" => array("maqh"=>"299","name"=>"Huyện Ninh Giang","matp"=>"30"),
	"213" => array("maqh"=>"300","name"=>"Huyện Thanh Miện","matp"=>"30"),
	"214" => array("maqh"=>"303","name"=>"Quận Hồng Bàng","matp"=>"31"),
	"215" => array("maqh"=>"304","name"=>"Quận Ngô Quyền","matp"=>"31"),
	"216" => array("maqh"=>"305","name"=>"Quận Lê Chân","matp"=>"31"),
	"217" => array("maqh"=>"306","name"=>"Quận Hải An","matp"=>"31"),
	"218" => array("maqh"=>"307","name"=>"Quận Kiến An","matp"=>"31"),
	"219" => array("maqh"=>"308","name"=>"Quận Đồ Sơn","matp"=>"31"),
	"220" => array("maqh"=>"309","name"=>"Quận Dương Kinh","matp"=>"31"),
	"221" => array("maqh"=>"311","name"=>"Huyện Thuỷ Nguyên","matp"=>"31"),
	"222" => array("maqh"=>"312","name"=>"Huyện An Dương","matp"=>"31"),
	"223" => array("maqh"=>"313","name"=>"Huyện An Lão","matp"=>"31"),
	"224" => array("maqh"=>"314","name"=>"Huyện Kiến Thuỵ","matp"=>"31"),
	"225" => array("maqh"=>"315","name"=>"Huyện Tiên Lãng","matp"=>"31"),
	"226" => array("maqh"=>"316","name"=>"Huyện Vĩnh Bảo","matp"=>"31"),
	"227" => array("maqh"=>"317","name"=>"Huyện Cát Hải","matp"=>"31"),
	"228" => array("maqh"=>"318","name"=>"Huyện Bạch Long Vĩ","matp"=>"31"),
	"229" => array("maqh"=>"323","name"=>"Thành phố Hưng Yên","matp"=>"33"),
	"230" => array("maqh"=>"325","name"=>"Huyện Văn Lâm","matp"=>"33"),
	"231" => array("maqh"=>"326","name"=>"Huyện Văn Giang","matp"=>"33"),
	"232" => array("maqh"=>"327","name"=>"Huyện Yên Mỹ","matp"=>"33"),
	"233" => array("maqh"=>"328","name"=>"Huyện Mỹ Hào","matp"=>"33"),
	"234" => array("maqh"=>"329","name"=>"Huyện Ân Thi","matp"=>"33"),
	"235" => array("maqh"=>"330","name"=>"Huyện Khoái Châu","matp"=>"33"),
	"236" => array("maqh"=>"331","name"=>"Huyện Kim Động","matp"=>"33"),
	"237" => array("maqh"=>"332","name"=>"Huyện Tiên Lữ","matp"=>"33"),
	"238" => array("maqh"=>"333","name"=>"Huyện Phù Cừ","matp"=>"33"),
	"239" => array("maqh"=>"336","name"=>"Thành phố Thái Bình","matp"=>"34"),
	"240" => array("maqh"=>"338","name"=>"Huyện Quỳnh Phụ","matp"=>"34"),
	"241" => array("maqh"=>"339","name"=>"Huyện Hưng Hà","matp"=>"34"),
	"242" => array("maqh"=>"340","name"=>"Huyện Đông Hưng","matp"=>"34"),
	"243" => array("maqh"=>"341","name"=>"Huyện Thái Thụy","matp"=>"34"),
	"244" => array("maqh"=>"342","name"=>"Huyện Tiền Hải","matp"=>"34"),
	"245" => array("maqh"=>"343","name"=>"Huyện Kiến Xương","matp"=>"34"),
	"246" => array("maqh"=>"344","name"=>"Huyện Vũ Thư","matp"=>"34"),
	"247" => array("maqh"=>"347","name"=>"Thành phố Phủ Lý","matp"=>"35"),
	"248" => array("maqh"=>"349","name"=>"Huyện Duy Tiên","matp"=>"35"),
	"249" => array("maqh"=>"350","name"=>"Huyện Kim Bảng","matp"=>"35"),
	"250" => array("maqh"=>"351","name"=>"Huyện Thanh Liêm","matp"=>"35"),
	"251" => array("maqh"=>"352","name"=>"Huyện Bình Lục","matp"=>"35"),
	"252" => array("maqh"=>"353","name"=>"Huyện Lý Nhân","matp"=>"35"),
	"253" => array("maqh"=>"356","name"=>"Thành phố Nam Định","matp"=>"36"),
	"254" => array("maqh"=>"358","name"=>"Huyện Mỹ Lộc","matp"=>"36"),
	"255" => array("maqh"=>"359","name"=>"Huyện Vụ Bản","matp"=>"36"),
	"256" => array("maqh"=>"360","name"=>"Huyện Ý Yên","matp"=>"36"),
	"257" => array("maqh"=>"361","name"=>"Huyện Nghĩa Hưng","matp"=>"36"),
	"258" => array("maqh"=>"362","name"=>"Huyện Nam Trực","matp"=>"36"),
	"259" => array("maqh"=>"363","name"=>"Huyện Trực Ninh","matp"=>"36"),
	"260" => array("maqh"=>"364","name"=>"Huyện Xuân Trường","matp"=>"36"),
	"261" => array("maqh"=>"365","name"=>"Huyện Giao Thủy","matp"=>"36"),
	"262" => array("maqh"=>"366","name"=>"Huyện Hải Hậu","matp"=>"36"),
	"263" => array("maqh"=>"369","name"=>"Thành phố Ninh Bình","matp"=>"37"),
	"264" => array("maqh"=>"370","name"=>"Thành phố Tam Điệp","matp"=>"37"),
	"265" => array("maqh"=>"372","name"=>"Huyện Nho Quan","matp"=>"37"),
	"266" => array("maqh"=>"373","name"=>"Huyện Gia Viễn","matp"=>"37"),
	"267" => array("maqh"=>"374","name"=>"Huyện Hoa Lư","matp"=>"37"),
	"268" => array("maqh"=>"375","name"=>"Huyện Yên Khánh","matp"=>"37"),
	"269" => array("maqh"=>"376","name"=>"Huyện Kim Sơn","matp"=>"37"),
	"270" => array("maqh"=>"377","name"=>"Huyện Yên Mô","matp"=>"37"),
	"271" => array("maqh"=>"380","name"=>"Thành phố Thanh Hóa","matp"=>"38"),
	"272" => array("maqh"=>"381","name"=>"Thị xã Bỉm Sơn","matp"=>"38"),
	"273" => array("maqh"=>"382","name"=>"Thị xã Sầm Sơn","matp"=>"38"),
	"274" => array("maqh"=>"384","name"=>"Huyện Mường Lát","matp"=>"38"),
	"275" => array("maqh"=>"385","name"=>"Huyện Quan Hóa","matp"=>"38"),
	"276" => array("maqh"=>"386","name"=>"Huyện Bá Thước","matp"=>"38"),
	"277" => array("maqh"=>"387","name"=>"Huyện Quan Sơn","matp"=>"38"),
	"278" => array("maqh"=>"388","name"=>"Huyện Lang Chánh","matp"=>"38"),
	"279" => array("maqh"=>"389","name"=>"Huyện Ngọc Lặc","matp"=>"38"),
	"280" => array("maqh"=>"390","name"=>"Huyện Cẩm Thủy","matp"=>"38"),
	"281" => array("maqh"=>"391","name"=>"Huyện Thạch Thành","matp"=>"38"),
	"282" => array("maqh"=>"392","name"=>"Huyện Hà Trung","matp"=>"38"),
	"283" => array("maqh"=>"393","name"=>"Huyện Vĩnh Lộc","matp"=>"38"),
	"284" => array("maqh"=>"394","name"=>"Huyện Yên Định","matp"=>"38"),
	"285" => array("maqh"=>"395","name"=>"Huyện Thọ Xuân","matp"=>"38"),
	"286" => array("maqh"=>"396","name"=>"Huyện Thường Xuân","matp"=>"38"),
	"287" => array("maqh"=>"397","name"=>"Huyện Triệu Sơn","matp"=>"38"),
	"288" => array("maqh"=>"398","name"=>"Huyện Thiệu Hóa","matp"=>"38"),
	"289" => array("maqh"=>"399","name"=>"Huyện Hoằng Hóa","matp"=>"38"),
	"290" => array("maqh"=>"400","name"=>"Huyện Hậu Lộc","matp"=>"38"),
	"291" => array("maqh"=>"401","name"=>"Huyện Nga Sơn","matp"=>"38"),
	"292" => array("maqh"=>"402","name"=>"Huyện Như Xuân","matp"=>"38"),
	"293" => array("maqh"=>"403","name"=>"Huyện Như Thanh","matp"=>"38"),
	"294" => array("maqh"=>"404","name"=>"Huyện Nông Cống","matp"=>"38"),
	"295" => array("maqh"=>"405","name"=>"Huyện Đông Sơn","matp"=>"38"),
	"296" => array("maqh"=>"406","name"=>"Huyện Quảng Xương","matp"=>"38"),
	"297" => array("maqh"=>"407","name"=>"Huyện Tĩnh Gia","matp"=>"38"),
	"298" => array("maqh"=>"412","name"=>"Thành phố Vinh","matp"=>"40"),
	"299" => array("maqh"=>"413","name"=>"Thị xã Cửa Lò","matp"=>"40"),
	"300" => array("maqh"=>"414","name"=>"Thị xã Thái Hoà","matp"=>"40"),
	"301" => array("maqh"=>"415","name"=>"Huyện Quế Phong","matp"=>"40"),
	"302" => array("maqh"=>"416","name"=>"Huyện Quỳ Châu","matp"=>"40"),
	"303" => array("maqh"=>"417","name"=>"Huyện Kỳ Sơn","matp"=>"40"),
	"304" => array("maqh"=>"418","name"=>"Huyện Tương Dương","matp"=>"40"),
	"305" => array("maqh"=>"419","name"=>"Huyện Nghĩa Đàn","matp"=>"40"),
	"306" => array("maqh"=>"420","name"=>"Huyện Quỳ Hợp","matp"=>"40"),
	"307" => array("maqh"=>"421","name"=>"Huyện Quỳnh Lưu","matp"=>"40"),
	"308" => array("maqh"=>"422","name"=>"Huyện Con Cuông","matp"=>"40"),
	"309" => array("maqh"=>"423","name"=>"Huyện Tân Kỳ","matp"=>"40"),
	"310" => array("maqh"=>"424","name"=>"Huyện Anh Sơn","matp"=>"40"),
	"311" => array("maqh"=>"425","name"=>"Huyện Diễn Châu","matp"=>"40"),
	"312" => array("maqh"=>"426","name"=>"Huyện Yên Thành","matp"=>"40"),
	"313" => array("maqh"=>"427","name"=>"Huyện Đô Lương","matp"=>"40"),
	"314" => array("maqh"=>"428","name"=>"Huyện Thanh Chương","matp"=>"40"),
	"315" => array("maqh"=>"429","name"=>"Huyện Nghi Lộc","matp"=>"40"),
	"316" => array("maqh"=>"430","name"=>"Huyện Nam Đàn","matp"=>"40"),
	"317" => array("maqh"=>"431","name"=>"Huyện Hưng Nguyên","matp"=>"40"),
	"318" => array("maqh"=>"432","name"=>"Thị xã Hoàng Mai","matp"=>"40"),
	"319" => array("maqh"=>"436","name"=>"Thành phố Hà Tĩnh","matp"=>"42"),
	"320" => array("maqh"=>"437","name"=>"Thị xã Hồng Lĩnh","matp"=>"42"),
	"321" => array("maqh"=>"439","name"=>"Huyện Hương Sơn","matp"=>"42"),
	"322" => array("maqh"=>"440","name"=>"Huyện Đức Thọ","matp"=>"42"),
	"323" => array("maqh"=>"441","name"=>"Huyện Vũ Quang","matp"=>"42"),
	"324" => array("maqh"=>"442","name"=>"Huyện Nghi Xuân","matp"=>"42"),
	"325" => array("maqh"=>"443","name"=>"Huyện Can Lộc","matp"=>"42"),
	"326" => array("maqh"=>"444","name"=>"Huyện Hương Khê","matp"=>"42"),
	"327" => array("maqh"=>"445","name"=>"Huyện Thạch Hà","matp"=>"42"),
	"328" => array("maqh"=>"446","name"=>"Huyện Cẩm Xuyên","matp"=>"42"),
	"329" => array("maqh"=>"447","name"=>"Huyện Kỳ Anh","matp"=>"42"),
	"330" => array("maqh"=>"448","name"=>"Huyện Lộc Hà","matp"=>"42"),
	"331" => array("maqh"=>"449","name"=>"Thị xã Kỳ Anh","matp"=>"42"),
	"332" => array("maqh"=>"450","name"=>"Thành Phố Đồng Hới","matp"=>"44"),
	"333" => array("maqh"=>"452","name"=>"Huyện Minh Hóa","matp"=>"44"),
	"334" => array("maqh"=>"453","name"=>"Huyện Tuyên Hóa","matp"=>"44"),
	"335" => array("maqh"=>"454","name"=>"Huyện Quảng Trạch","matp"=>"44"),
	"336" => array("maqh"=>"455","name"=>"Huyện Bố Trạch","matp"=>"44"),
	"337" => array("maqh"=>"456","name"=>"Huyện Quảng Ninh","matp"=>"44"),
	"338" => array("maqh"=>"457","name"=>"Huyện Lệ Thủy","matp"=>"44"),
	"339" => array("maqh"=>"458","name"=>"Thị xã Ba Đồn","matp"=>"44"),
	"340" => array("maqh"=>"461","name"=>"Thành phố Đông Hà","matp"=>"45"),
	"341" => array("maqh"=>"462","name"=>"Thị xã Quảng Trị","matp"=>"45"),
	"342" => array("maqh"=>"464","name"=>"Huyện Vĩnh Linh","matp"=>"45"),
	"343" => array("maqh"=>"465","name"=>"Huyện Hướng Hóa","matp"=>"45"),
	"344" => array("maqh"=>"466","name"=>"Huyện Gio Linh","matp"=>"45"),
	"345" => array("maqh"=>"467","name"=>"Huyện Đa Krông","matp"=>"45"),
	"346" => array("maqh"=>"468","name"=>"Huyện Cam Lộ","matp"=>"45"),
	"347" => array("maqh"=>"469","name"=>"Huyện Triệu Phong","matp"=>"45"),
	"348" => array("maqh"=>"470","name"=>"Huyện Hải Lăng","matp"=>"45"),
	"349" => array("maqh"=>"471","name"=>"Huyện Cồn Cỏ","matp"=>"45"),
	"350" => array("maqh"=>"474","name"=>"Thành phố Huế","matp"=>"46"),
	"351" => array("maqh"=>"476","name"=>"Huyện Phong Điền","matp"=>"46"),
	"352" => array("maqh"=>"477","name"=>"Huyện Quảng Điền","matp"=>"46"),
	"353" => array("maqh"=>"478","name"=>"Huyện Phú Vang","matp"=>"46"),
	"354" => array("maqh"=>"479","name"=>"Thị xã Hương Thủy","matp"=>"46"),
	"355" => array("maqh"=>"480","name"=>"Thị xã Hương Trà","matp"=>"46"),
	"356" => array("maqh"=>"481","name"=>"Huyện A Lưới","matp"=>"46"),
	"357" => array("maqh"=>"482","name"=>"Huyện Phú Lộc","matp"=>"46"),
	"358" => array("maqh"=>"483","name"=>"Huyện Nam Đông","matp"=>"46"),
	"359" => array("maqh"=>"490","name"=>"Quận Liên Chiểu","matp"=>"48"),
	"360" => array("maqh"=>"491","name"=>"Quận Thanh Khê","matp"=>"48"),
	"361" => array("maqh"=>"492","name"=>"Quận Hải Châu","matp"=>"48"),
	"362" => array("maqh"=>"493","name"=>"Quận Sơn Trà","matp"=>"48"),
	"363" => array("maqh"=>"494","name"=>"Quận Ngũ Hành Sơn","matp"=>"48"),
	"364" => array("maqh"=>"495","name"=>"Quận Cẩm Lệ","matp"=>"48"),
	"365" => array("maqh"=>"497","name"=>"Huyện Hòa Vang","matp"=>"48"),
	"366" => array("maqh"=>"498","name"=>"Huyện Hoàng Sa","matp"=>"48"),
	"367" => array("maqh"=>"502","name"=>"Thành phố Tam Kỳ","matp"=>"49"),
	"368" => array("maqh"=>"503","name"=>"Thành phố Hội An","matp"=>"49"),
	"369" => array("maqh"=>"504","name"=>"Huyện Tây Giang","matp"=>"49"),
	"370" => array("maqh"=>"505","name"=>"Huyện Đông Giang","matp"=>"49"),
	"371" => array("maqh"=>"506","name"=>"Huyện Đại Lộc","matp"=>"49"),
	"372" => array("maqh"=>"507","name"=>"Thị xã Điện Bàn","matp"=>"49"),
	"373" => array("maqh"=>"508","name"=>"Huyện Duy Xuyên","matp"=>"49"),
	"374" => array("maqh"=>"509","name"=>"Huyện Quế Sơn","matp"=>"49"),
	"375" => array("maqh"=>"510","name"=>"Huyện Nam Giang","matp"=>"49"),
	"376" => array("maqh"=>"511","name"=>"Huyện Phước Sơn","matp"=>"49"),
	"377" => array("maqh"=>"512","name"=>"Huyện Hiệp Đức","matp"=>"49"),
	"378" => array("maqh"=>"513","name"=>"Huyện Thăng Bình","matp"=>"49"),
	"379" => array("maqh"=>"514","name"=>"Huyện Tiên Phước","matp"=>"49"),
	"380" => array("maqh"=>"515","name"=>"Huyện Bắc Trà My","matp"=>"49"),
	"381" => array("maqh"=>"516","name"=>"Huyện Nam Trà My","matp"=>"49"),
	"382" => array("maqh"=>"517","name"=>"Huyện Núi Thành","matp"=>"49"),
	"383" => array("maqh"=>"518","name"=>"Huyện Phú Ninh","matp"=>"49"),
	"384" => array("maqh"=>"519","name"=>"Huyện Nông Sơn","matp"=>"49"),
	"385" => array("maqh"=>"522","name"=>"Thành phố Quảng Ngãi","matp"=>"51"),
	"386" => array("maqh"=>"524","name"=>"Huyện Bình Sơn","matp"=>"51"),
	"387" => array("maqh"=>"525","name"=>"Huyện Trà Bồng","matp"=>"51"),
	"388" => array("maqh"=>"526","name"=>"Huyện Tây Trà","matp"=>"51"),
	"389" => array("maqh"=>"527","name"=>"Huyện Sơn Tịnh","matp"=>"51"),
	"390" => array("maqh"=>"528","name"=>"Huyện Tư Nghĩa","matp"=>"51"),
	"391" => array("maqh"=>"529","name"=>"Huyện Sơn Hà","matp"=>"51"),
	"392" => array("maqh"=>"530","name"=>"Huyện Sơn Tây","matp"=>"51"),
	"393" => array("maqh"=>"531","name"=>"Huyện Minh Long","matp"=>"51"),
	"394" => array("maqh"=>"532","name"=>"Huyện Nghĩa Hành","matp"=>"51"),
	"395" => array("maqh"=>"533","name"=>"Huyện Mộ Đức","matp"=>"51"),
	"396" => array("maqh"=>"534","name"=>"Huyện Đức Phổ","matp"=>"51"),
	"397" => array("maqh"=>"535","name"=>"Huyện Ba Tơ","matp"=>"51"),
	"398" => array("maqh"=>"536","name"=>"Huyện Lý Sơn","matp"=>"51"),
	"399" => array("maqh"=>"540","name"=>"Thành phố Qui Nhơn","matp"=>"52"),
	"400" => array("maqh"=>"542","name"=>"Huyện An Lão","matp"=>"52"),
	"401" => array("maqh"=>"543","name"=>"Huyện Hoài Nhơn","matp"=>"52"),
	"402" => array("maqh"=>"544","name"=>"Huyện Hoài Ân","matp"=>"52"),
	"403" => array("maqh"=>"545","name"=>"Huyện Phù Mỹ","matp"=>"52"),
	"404" => array("maqh"=>"546","name"=>"Huyện Vĩnh Thạnh","matp"=>"52"),
	"405" => array("maqh"=>"547","name"=>"Huyện Tây Sơn","matp"=>"52"),
	"406" => array("maqh"=>"548","name"=>"Huyện Phù Cát","matp"=>"52"),
	"407" => array("maqh"=>"549","name"=>"Thị xã An Nhơn","matp"=>"52"),
	"408" => array("maqh"=>"550","name"=>"Huyện Tuy Phước","matp"=>"52"),
	"409" => array("maqh"=>"551","name"=>"Huyện Vân Canh","matp"=>"52"),
	"410" => array("maqh"=>"555","name"=>"Thành phố Tuy Hoà","matp"=>"54"),
	"411" => array("maqh"=>"557","name"=>"Thị xã Sông Cầu","matp"=>"54"),
	"412" => array("maqh"=>"558","name"=>"Huyện Đồng Xuân","matp"=>"54"),
	"413" => array("maqh"=>"559","name"=>"Huyện Tuy An","matp"=>"54"),
	"414" => array("maqh"=>"560","name"=>"Huyện Sơn Hòa","matp"=>"54"),
	"415" => array("maqh"=>"561","name"=>"Huyện Sông Hinh","matp"=>"54"),
	"416" => array("maqh"=>"562","name"=>"Huyện Tây Hoà","matp"=>"54"),
	"417" => array("maqh"=>"563","name"=>"Huyện Phú Hoà","matp"=>"54"),
	"418" => array("maqh"=>"564","name"=>"Huyện Đông Hòa","matp"=>"54"),
	"419" => array("maqh"=>"568","name"=>"Thành phố Nha Trang","matp"=>"56"),
	"420" => array("maqh"=>"569","name"=>"Thành phố Cam Ranh","matp"=>"56"),
	"421" => array("maqh"=>"570","name"=>"Huyện Cam Lâm","matp"=>"56"),
	"422" => array("maqh"=>"571","name"=>"Huyện Vạn Ninh","matp"=>"56"),
	"423" => array("maqh"=>"572","name"=>"Thị xã Ninh Hòa","matp"=>"56"),
	"424" => array("maqh"=>"573","name"=>"Huyện Khánh Vĩnh","matp"=>"56"),
	"425" => array("maqh"=>"574","name"=>"Huyện Diên Khánh","matp"=>"56"),
	"426" => array("maqh"=>"575","name"=>"Huyện Khánh Sơn","matp"=>"56"),
	"427" => array("maqh"=>"576","name"=>"Huyện Trường Sa","matp"=>"56"),
	"428" => array("maqh"=>"582","name"=>"Thành phố Phan Rang-Tháp Chàm","matp"=>"58"),
	"429" => array("maqh"=>"584","name"=>"Huyện Bác Ái","matp"=>"58"),
	"430" => array("maqh"=>"585","name"=>"Huyện Ninh Sơn","matp"=>"58"),
	"431" => array("maqh"=>"586","name"=>"Huyện Ninh Hải","matp"=>"58"),
	"432" => array("maqh"=>"587","name"=>"Huyện Ninh Phước","matp"=>"58"),
	"433" => array("maqh"=>"588","name"=>"Huyện Thuận Bắc","matp"=>"58"),
	"434" => array("maqh"=>"589","name"=>"Huyện Thuận Nam","matp"=>"58"),
	"435" => array("maqh"=>"593","name"=>"Thành phố Phan Thiết","matp"=>"60"),
	"436" => array("maqh"=>"594","name"=>"Thị xã La Gi","matp"=>"60"),
	"437" => array("maqh"=>"595","name"=>"Huyện Tuy Phong","matp"=>"60"),
	"438" => array("maqh"=>"596","name"=>"Huyện Bắc Bình","matp"=>"60"),
	"439" => array("maqh"=>"597","name"=>"Huyện Hàm Thuận Bắc","matp"=>"60"),
	"440" => array("maqh"=>"598","name"=>"Huyện Hàm Thuận Nam","matp"=>"60"),
	"441" => array("maqh"=>"599","name"=>"Huyện Tánh Linh","matp"=>"60"),
	"442" => array("maqh"=>"600","name"=>"Huyện Đức Linh","matp"=>"60"),
	"443" => array("maqh"=>"601","name"=>"Huyện Hàm Tân","matp"=>"60"),
	"444" => array("maqh"=>"602","name"=>"Huyện Phú Quí","matp"=>"60"),
	"445" => array("maqh"=>"608","name"=>"Thành phố Kon Tum","matp"=>"62"),
	"446" => array("maqh"=>"610","name"=>"Huyện Đắk Glei","matp"=>"62"),
	"447" => array("maqh"=>"611","name"=>"Huyện Ngọc Hồi","matp"=>"62"),
	"448" => array("maqh"=>"612","name"=>"Huyện Đắk Tô","matp"=>"62"),
	"449" => array("maqh"=>"613","name"=>"Huyện Kon Plông","matp"=>"62"),
	"450" => array("maqh"=>"614","name"=>"Huyện Kon Rẫy","matp"=>"62"),
	"451" => array("maqh"=>"615","name"=>"Huyện Đắk Hà","matp"=>"62"),
	"452" => array("maqh"=>"616","name"=>"Huyện Sa Thầy","matp"=>"62"),
	"453" => array("maqh"=>"617","name"=>"Huyện Tu Mơ Rông","matp"=>"62"),
	"454" => array("maqh"=>"618","name"=>"Huyện Ia H' Drai","matp"=>"62"),
	"455" => array("maqh"=>"622","name"=>"Thành phố Pleiku","matp"=>"64"),
	"456" => array("maqh"=>"623","name"=>"Thị xã An Khê","matp"=>"64"),
	"457" => array("maqh"=>"624","name"=>"Thị xã Ayun Pa","matp"=>"64"),
	"458" => array("maqh"=>"625","name"=>"Huyện KBang","matp"=>"64"),
	"459" => array("maqh"=>"626","name"=>"Huyện Đăk Đoa","matp"=>"64"),
	"460" => array("maqh"=>"627","name"=>"Huyện Chư Păh","matp"=>"64"),
	"461" => array("maqh"=>"628","name"=>"Huyện Ia Grai","matp"=>"64"),
	"462" => array("maqh"=>"629","name"=>"Huyện Mang Yang","matp"=>"64"),
	"463" => array("maqh"=>"630","name"=>"Huyện Kông Chro","matp"=>"64"),
	"464" => array("maqh"=>"631","name"=>"Huyện Đức Cơ","matp"=>"64"),
	"465" => array("maqh"=>"632","name"=>"Huyện Chư Prông","matp"=>"64"),
	"466" => array("maqh"=>"633","name"=>"Huyện Chư Sê","matp"=>"64"),
	"467" => array("maqh"=>"634","name"=>"Huyện Đăk Pơ","matp"=>"64"),
	"468" => array("maqh"=>"635","name"=>"Huyện Ia Pa","matp"=>"64"),
	"469" => array("maqh"=>"637","name"=>"Huyện Krông Pa","matp"=>"64"),
	"470" => array("maqh"=>"638","name"=>"Huyện Phú Thiện","matp"=>"64"),
	"471" => array("maqh"=>"639","name"=>"Huyện Chư Pưh","matp"=>"64"),
	"472" => array("maqh"=>"643","name"=>"Thành phố Buôn Ma Thuột","matp"=>"66"),
	"473" => array("maqh"=>"644","name"=>"Thị Xã Buôn Hồ","matp"=>"66"),
	"474" => array("maqh"=>"645","name"=>"Huyện Ea H'leo","matp"=>"66"),
	"475" => array("maqh"=>"646","name"=>"Huyện Ea Súp","matp"=>"66"),
	"476" => array("maqh"=>"647","name"=>"Huyện Buôn Đôn","matp"=>"66"),
	"477" => array("maqh"=>"648","name"=>"Huyện Cư M'gar","matp"=>"66"),
	"478" => array("maqh"=>"649","name"=>"Huyện Krông Búk","matp"=>"66"),
	"479" => array("maqh"=>"650","name"=>"Huyện Krông Năng","matp"=>"66"),
	"480" => array("maqh"=>"651","name"=>"Huyện Ea Kar","matp"=>"66"),
	"481" => array("maqh"=>"652","name"=>"Huyện M'Đrắk","matp"=>"66"),
	"482" => array("maqh"=>"653","name"=>"Huyện Krông Bông","matp"=>"66"),
	"483" => array("maqh"=>"654","name"=>"Huyện Krông Pắc","matp"=>"66"),
	"484" => array("maqh"=>"655","name"=>"Huyện Krông A Na","matp"=>"66"),
	"485" => array("maqh"=>"656","name"=>"Huyện Lắk","matp"=>"66"),
	"486" => array("maqh"=>"657","name"=>"Huyện Cư Kuin","matp"=>"66"),
	"487" => array("maqh"=>"660","name"=>"Thị xã Gia Nghĩa","matp"=>"67"),
	"488" => array("maqh"=>"661","name"=>"Huyện Đăk Glong","matp"=>"67"),
	"489" => array("maqh"=>"662","name"=>"Huyện Cư Jút","matp"=>"67"),
	"490" => array("maqh"=>"663","name"=>"Huyện Đắk Mil","matp"=>"67"),
	"491" => array("maqh"=>"664","name"=>"Huyện Krông Nô","matp"=>"67"),
	"492" => array("maqh"=>"665","name"=>"Huyện Đắk Song","matp"=>"67"),
	"493" => array("maqh"=>"666","name"=>"Huyện Đắk R'Lấp","matp"=>"67"),
	"494" => array("maqh"=>"667","name"=>"Huyện Tuy Đức","matp"=>"67"),
	"495" => array("maqh"=>"672","name"=>"Thành phố Đà Lạt","matp"=>"68"),
	"496" => array("maqh"=>"673","name"=>"Thành phố Bảo Lộc","matp"=>"68"),
	"497" => array("maqh"=>"674","name"=>"Huyện Đam Rông","matp"=>"68"),
	"498" => array("maqh"=>"675","name"=>"Huyện Lạc Dương","matp"=>"68"),
	"499" => array("maqh"=>"676","name"=>"Huyện Lâm Hà","matp"=>"68"),
	"500" => array("maqh"=>"677","name"=>"Huyện Đơn Dương","matp"=>"68"),
	"501" => array("maqh"=>"678","name"=>"Huyện Đức Trọng","matp"=>"68"),
	"502" => array("maqh"=>"679","name"=>"Huyện Di Linh","matp"=>"68"),
	"503" => array("maqh"=>"680","name"=>"Huyện Bảo Lâm","matp"=>"68"),
	"504" => array("maqh"=>"681","name"=>"Huyện Đạ Huoai","matp"=>"68"),
	"505" => array("maqh"=>"682","name"=>"Huyện Đạ Tẻh","matp"=>"68"),
	"506" => array("maqh"=>"683","name"=>"Huyện Cát Tiên","matp"=>"68"),
	"507" => array("maqh"=>"688","name"=>"Thị xã Phước Long","matp"=>"70"),
	"508" => array("maqh"=>"689","name"=>"Thị xã Đồng Xoài","matp"=>"70"),
	"509" => array("maqh"=>"690","name"=>"Thị xã Bình Long","matp"=>"70"),
	"510" => array("maqh"=>"691","name"=>"Huyện Bù Gia Mập","matp"=>"70"),
	"511" => array("maqh"=>"692","name"=>"Huyện Lộc Ninh","matp"=>"70"),
	"512" => array("maqh"=>"693","name"=>"Huyện Bù Đốp","matp"=>"70"),
	"513" => array("maqh"=>"694","name"=>"Huyện Hớn Quản","matp"=>"70"),
	"514" => array("maqh"=>"695","name"=>"Huyện Đồng Phú","matp"=>"70"),
	"515" => array("maqh"=>"696","name"=>"Huyện Bù Đăng","matp"=>"70"),
	"516" => array("maqh"=>"697","name"=>"Huyện Chơn Thành","matp"=>"70"),
	"517" => array("maqh"=>"698","name"=>"Huyện Phú Riềng","matp"=>"70"),
	"518" => array("maqh"=>"703","name"=>"Thành phố Tây Ninh","matp"=>"72"),
	"519" => array("maqh"=>"705","name"=>"Huyện Tân Biên","matp"=>"72"),
	"520" => array("maqh"=>"706","name"=>"Huyện Tân Châu","matp"=>"72"),
	"521" => array("maqh"=>"707","name"=>"Huyện Dương Minh Châu","matp"=>"72"),
	"522" => array("maqh"=>"708","name"=>"Huyện Châu Thành","matp"=>"72"),
	"523" => array("maqh"=>"709","name"=>"Huyện Hòa Thành","matp"=>"72"),
	"524" => array("maqh"=>"710","name"=>"Huyện Gò Dầu","matp"=>"72"),
	"525" => array("maqh"=>"711","name"=>"Huyện Bến Cầu","matp"=>"72"),
	"526" => array("maqh"=>"712","name"=>"Huyện Trảng Bàng","matp"=>"72"),
	"527" => array("maqh"=>"718","name"=>"Thành phố Thủ Dầu Một","matp"=>"74"),
	"528" => array("maqh"=>"719","name"=>"Huyện Bàu Bàng","matp"=>"74"),
	"529" => array("maqh"=>"720","name"=>"Huyện Dầu Tiếng","matp"=>"74"),
	"530" => array("maqh"=>"721","name"=>"Thị xã Bến Cát","matp"=>"74"),
	"531" => array("maqh"=>"722","name"=>"Huyện Phú Giáo","matp"=>"74"),
	"532" => array("maqh"=>"723","name"=>"Thị xã Tân Uyên","matp"=>"74"),
	"533" => array("maqh"=>"724","name"=>"Thị xã Dĩ An","matp"=>"74"),
	"534" => array("maqh"=>"725","name"=>"Thị xã Thuận An","matp"=>"74"),
	"535" => array("maqh"=>"726","name"=>"Huyện Bắc Tân Uyên","matp"=>"74"),
	"536" => array("maqh"=>"731","name"=>"Thành phố Biên Hòa","matp"=>"75"),
	"537" => array("maqh"=>"732","name"=>"Thị xã Long Khánh","matp"=>"75"),
	"538" => array("maqh"=>"734","name"=>"Huyện Tân Phú","matp"=>"75"),
	"539" => array("maqh"=>"735","name"=>"Huyện Vĩnh Cửu","matp"=>"75"),
	"540" => array("maqh"=>"736","name"=>"Huyện Định Quán","matp"=>"75"),
	"541" => array("maqh"=>"737","name"=>"Huyện Trảng Bom","matp"=>"75"),
	"542" => array("maqh"=>"738","name"=>"Huyện Thống Nhất","matp"=>"75"),
	"543" => array("maqh"=>"739","name"=>"Huyện Cẩm Mỹ","matp"=>"75"),
	"544" => array("maqh"=>"740","name"=>"Huyện Long Thành","matp"=>"75"),
	"545" => array("maqh"=>"741","name"=>"Huyện Xuân Lộc","matp"=>"75"),
	"546" => array("maqh"=>"742","name"=>"Huyện Nhơn Trạch","matp"=>"75"),
	"547" => array("maqh"=>"747","name"=>"Thành phố Vũng Tàu","matp"=>"77"),
	"548" => array("maqh"=>"748","name"=>"Thành phố Bà Rịa","matp"=>"77"),
	"549" => array("maqh"=>"750","name"=>"Huyện Châu Đức","matp"=>"77"),
	"550" => array("maqh"=>"751","name"=>"Huyện Xuyên Mộc","matp"=>"77"),
	"551" => array("maqh"=>"752","name"=>"Huyện Long Điền","matp"=>"77"),
	"552" => array("maqh"=>"753","name"=>"Huyện Đất Đỏ","matp"=>"77"),
	"553" => array("maqh"=>"754","name"=>"Huyện Tân Thành","matp"=>"77"),
	"554" => array("maqh"=>"755","name"=>"Huyện Côn Đảo","matp"=>"77"),
	"555" => array("maqh"=>"760","name"=>"Quận 1","matp"=>"79"),
	"556" => array("maqh"=>"761","name"=>"Quận 12","matp"=>"79"),
	"557" => array("maqh"=>"762","name"=>"Quận Thủ Đức","matp"=>"79"),
	"558" => array("maqh"=>"763","name"=>"Quận 9","matp"=>"79"),
	"559" => array("maqh"=>"764","name"=>"Quận Gò Vấp","matp"=>"79"),
	"560" => array("maqh"=>"765","name"=>"Quận Bình Thạnh","matp"=>"79"),
	"561" => array("maqh"=>"766","name"=>"Quận Tân Bình","matp"=>"79"),
	"562" => array("maqh"=>"767","name"=>"Quận Tân Phú","matp"=>"79"),
	"563" => array("maqh"=>"768","name"=>"Quận Phú Nhuận","matp"=>"79"),
	"564" => array("maqh"=>"769","name"=>"Quận 2","matp"=>"79"),
	"565" => array("maqh"=>"770","name"=>"Quận 3","matp"=>"79"),
	"566" => array("maqh"=>"771","name"=>"Quận 10","matp"=>"79"),
	"567" => array("maqh"=>"772","name"=>"Quận 11","matp"=>"79"),
	"568" => array("maqh"=>"773","name"=>"Quận 4","matp"=>"79"),
	"569" => array("maqh"=>"774","name"=>"Quận 5","matp"=>"79"),
	"570" => array("maqh"=>"775","name"=>"Quận 6","matp"=>"79"),
	"571" => array("maqh"=>"776","name"=>"Quận 8","matp"=>"79"),
	"572" => array("maqh"=>"777","name"=>"Quận Bình Tân","matp"=>"79"),
	"573" => array("maqh"=>"778","name"=>"Quận 7","matp"=>"79"),
	"574" => array("maqh"=>"783","name"=>"Huyện Củ Chi","matp"=>"79"),
	"575" => array("maqh"=>"784","name"=>"Huyện Hóc Môn","matp"=>"79"),
	"576" => array("maqh"=>"785","name"=>"Huyện Bình Chánh","matp"=>"79"),
	"577" => array("maqh"=>"786","name"=>"Huyện Nhà Bè","matp"=>"79"),
	"578" => array("maqh"=>"787","name"=>"Huyện Cần Giờ","matp"=>"79"),
	"579" => array("maqh"=>"794","name"=>"Thành phố Tân An","matp"=>"80"),
	"580" => array("maqh"=>"795","name"=>"Thị xã Kiến Tường","matp"=>"80"),
	"581" => array("maqh"=>"796","name"=>"Huyện Tân Hưng","matp"=>"80"),
	"582" => array("maqh"=>"797","name"=>"Huyện Vĩnh Hưng","matp"=>"80"),
	"583" => array("maqh"=>"798","name"=>"Huyện Mộc Hóa","matp"=>"80"),
	"584" => array("maqh"=>"799","name"=>"Huyện Tân Thạnh","matp"=>"80"),
	"585" => array("maqh"=>"800","name"=>"Huyện Thạnh Hóa","matp"=>"80"),
	"586" => array("maqh"=>"801","name"=>"Huyện Đức Huệ","matp"=>"80"),
	"587" => array("maqh"=>"802","name"=>"Huyện Đức Hòa","matp"=>"80"),
	"588" => array("maqh"=>"803","name"=>"Huyện Bến Lức","matp"=>"80"),
	"589" => array("maqh"=>"804","name"=>"Huyện Thủ Thừa","matp"=>"80"),
	"590" => array("maqh"=>"805","name"=>"Huyện Tân Trụ","matp"=>"80"),
	"591" => array("maqh"=>"806","name"=>"Huyện Cần Đước","matp"=>"80"),
	"592" => array("maqh"=>"807","name"=>"Huyện Cần Giuộc","matp"=>"80"),
	"593" => array("maqh"=>"808","name"=>"Huyện Châu Thành","matp"=>"80"),
	"594" => array("maqh"=>"815","name"=>"Thành phố Mỹ Tho","matp"=>"82"),
	"595" => array("maqh"=>"816","name"=>"Thị xã Gò Công","matp"=>"82"),
	"596" => array("maqh"=>"817","name"=>"Thị xã Cai Lậy","matp"=>"82"),
	"597" => array("maqh"=>"818","name"=>"Huyện Tân Phước","matp"=>"82"),
	"598" => array("maqh"=>"819","name"=>"Huyện Cái Bè","matp"=>"82"),
	"599" => array("maqh"=>"820","name"=>"Huyện Cai Lậy","matp"=>"82"),
	"600" => array("maqh"=>"821","name"=>"Huyện Châu Thành","matp"=>"82"),
	"601" => array("maqh"=>"822","name"=>"Huyện Chợ Gạo","matp"=>"82"),
	"602" => array("maqh"=>"823","name"=>"Huyện Gò Công Tây","matp"=>"82"),
	"603" => array("maqh"=>"824","name"=>"Huyện Gò Công Đông","matp"=>"82"),
	"604" => array("maqh"=>"825","name"=>"Huyện Tân Phú Đông","matp"=>"82"),
	"605" => array("maqh"=>"829","name"=>"Thành phố Bến Tre","matp"=>"83"),
	"606" => array("maqh"=>"831","name"=>"Huyện Châu Thành","matp"=>"83"),
	"607" => array("maqh"=>"832","name"=>"Huyện Chợ Lách","matp"=>"83"),
	"608" => array("maqh"=>"833","name"=>"Huyện Mỏ Cày Nam","matp"=>"83"),
	"609" => array("maqh"=>"834","name"=>"Huyện Giồng Trôm","matp"=>"83"),
	"610" => array("maqh"=>"835","name"=>"Huyện Bình Đại","matp"=>"83"),
	"611" => array("maqh"=>"836","name"=>"Huyện Ba Tri","matp"=>"83"),
	"612" => array("maqh"=>"837","name"=>"Huyện Thạnh Phú","matp"=>"83"),
	"613" => array("maqh"=>"838","name"=>"Huyện Mỏ Cày Bắc","matp"=>"83"),
	"614" => array("maqh"=>"842","name"=>"Thành phố Trà Vinh","matp"=>"84"),
	"615" => array("maqh"=>"844","name"=>"Huyện Càng Long","matp"=>"84"),
	"616" => array("maqh"=>"845","name"=>"Huyện Cầu Kè","matp"=>"84"),
	"617" => array("maqh"=>"846","name"=>"Huyện Tiểu Cần","matp"=>"84"),
	"618" => array("maqh"=>"847","name"=>"Huyện Châu Thành","matp"=>"84"),
	"619" => array("maqh"=>"848","name"=>"Huyện Cầu Ngang","matp"=>"84"),
	"620" => array("maqh"=>"849","name"=>"Huyện Trà Cú","matp"=>"84"),
	"621" => array("maqh"=>"850","name"=>"Huyện Duyên Hải","matp"=>"84"),
	"622" => array("maqh"=>"851","name"=>"Thị xã Duyên Hải","matp"=>"84"),
	"623" => array("maqh"=>"855","name"=>"Thành phố Vĩnh Long","matp"=>"86"),
	"624" => array("maqh"=>"857","name"=>"Huyện Long Hồ","matp"=>"86"),
	"625" => array("maqh"=>"858","name"=>"Huyện Mang Thít","matp"=>"86"),
	"626" => array("maqh"=>"859","name"=>"Huyện Vũng Liêm","matp"=>"86"),
	"627" => array("maqh"=>"860","name"=>"Huyện Tam Bình","matp"=>"86"),
	"628" => array("maqh"=>"861","name"=>"Thị xã Bình Minh","matp"=>"86"),
	"629" => array("maqh"=>"862","name"=>"Huyện Trà Ôn","matp"=>"86"),
	"630" => array("maqh"=>"863","name"=>"Huyện Bình Tân","matp"=>"86"),
	"631" => array("maqh"=>"866","name"=>"Thành phố Cao Lãnh","matp"=>"87"),
	"632" => array("maqh"=>"867","name"=>"Thành phố Sa Đéc","matp"=>"87"),
	"633" => array("maqh"=>"868","name"=>"Thị xã Hồng Ngự","matp"=>"87"),
	"634" => array("maqh"=>"869","name"=>"Huyện Tân Hồng","matp"=>"87"),
	"635" => array("maqh"=>"870","name"=>"Huyện Hồng Ngự","matp"=>"87"),
	"636" => array("maqh"=>"871","name"=>"Huyện Tam Nông","matp"=>"87"),
	"637" => array("maqh"=>"872","name"=>"Huyện Tháp Mười","matp"=>"87"),
	"638" => array("maqh"=>"873","name"=>"Huyện Cao Lãnh","matp"=>"87"),
	"639" => array("maqh"=>"874","name"=>"Huyện Thanh Bình","matp"=>"87"),
	"640" => array("maqh"=>"875","name"=>"Huyện Lấp Vò","matp"=>"87"),
	"641" => array("maqh"=>"876","name"=>"Huyện Lai Vung","matp"=>"87"),
	"642" => array("maqh"=>"877","name"=>"Huyện Châu Thành","matp"=>"87"),
	"643" => array("maqh"=>"883","name"=>"Thành phố Long Xuyên","matp"=>"89"),
	"644" => array("maqh"=>"884","name"=>"Thành phố Châu Đốc","matp"=>"89"),
	"645" => array("maqh"=>"886","name"=>"Huyện An Phú","matp"=>"89"),
	"646" => array("maqh"=>"887","name"=>"Thị xã Tân Châu","matp"=>"89"),
	"647" => array("maqh"=>"888","name"=>"Huyện Phú Tân","matp"=>"89"),
	"648" => array("maqh"=>"889","name"=>"Huyện Châu Phú","matp"=>"89"),
	"649" => array("maqh"=>"890","name"=>"Huyện Tịnh Biên","matp"=>"89"),
	"650" => array("maqh"=>"891","name"=>"Huyện Tri Tôn","matp"=>"89"),
	"651" => array("maqh"=>"892","name"=>"Huyện Châu Thành","matp"=>"89"),
	"652" => array("maqh"=>"893","name"=>"Huyện Chợ Mới","matp"=>"89"),
	"653" => array("maqh"=>"894","name"=>"Huyện Thoại Sơn","matp"=>"89"),
	"654" => array("maqh"=>"899","name"=>"Thành phố Rạch Giá","matp"=>"91"),
	"655" => array("maqh"=>"900","name"=>"Thị xã Hà Tiên","matp"=>"91"),
	"656" => array("maqh"=>"902","name"=>"Huyện Kiên Lương","matp"=>"91"),
	"657" => array("maqh"=>"903","name"=>"Huyện Hòn Đất","matp"=>"91"),
	"658" => array("maqh"=>"904","name"=>"Huyện Tân Hiệp","matp"=>"91"),
	"659" => array("maqh"=>"905","name"=>"Huyện Châu Thành","matp"=>"91"),
	"660" => array("maqh"=>"906","name"=>"Huyện Giồng Riềng","matp"=>"91"),
	"661" => array("maqh"=>"907","name"=>"Huyện Gò Quao","matp"=>"91"),
	"662" => array("maqh"=>"908","name"=>"Huyện An Biên","matp"=>"91"),
	"663" => array("maqh"=>"909","name"=>"Huyện An Minh","matp"=>"91"),
	"664" => array("maqh"=>"910","name"=>"Huyện Vĩnh Thuận","matp"=>"91"),
	"665" => array("maqh"=>"911","name"=>"Huyện Phú Quốc","matp"=>"91"),
	"666" => array("maqh"=>"912","name"=>"Huyện Kiên Hải","matp"=>"91"),
	"667" => array("maqh"=>"913","name"=>"Huyện U Minh Thượng","matp"=>"91"),
	"668" => array("maqh"=>"914","name"=>"Huyện Giang Thành","matp"=>"91"),
	"669" => array("maqh"=>"916","name"=>"Quận Ninh Kiều","matp"=>"92"),
	"670" => array("maqh"=>"917","name"=>"Quận Ô Môn","matp"=>"92"),
	"671" => array("maqh"=>"918","name"=>"Quận Bình Thuỷ","matp"=>"92"),
	"672" => array("maqh"=>"919","name"=>"Quận Cái Răng","matp"=>"92"),
	"673" => array("maqh"=>"923","name"=>"Quận Thốt Nốt","matp"=>"92"),
	"674" => array("maqh"=>"924","name"=>"Huyện Vĩnh Thạnh","matp"=>"92"),
	"675" => array("maqh"=>"925","name"=>"Huyện Cờ Đỏ","matp"=>"92"),
	"676" => array("maqh"=>"926","name"=>"Huyện Phong Điền","matp"=>"92"),
	"677" => array("maqh"=>"927","name"=>"Huyện Thới Lai","matp"=>"92"),
	"678" => array("maqh"=>"930","name"=>"Thành phố Vị Thanh","matp"=>"93"),
	"679" => array("maqh"=>"931","name"=>"Thị xã Ngã Bảy","matp"=>"93"),
	"680" => array("maqh"=>"932","name"=>"Huyện Châu Thành A","matp"=>"93"),
	"681" => array("maqh"=>"933","name"=>"Huyện Châu Thành","matp"=>"93"),
	"682" => array("maqh"=>"934","name"=>"Huyện Phụng Hiệp","matp"=>"93"),
	"683" => array("maqh"=>"935","name"=>"Huyện Vị Thuỷ","matp"=>"93"),
	"684" => array("maqh"=>"936","name"=>"Huyện Long Mỹ","matp"=>"93"),
	"685" => array("maqh"=>"937","name"=>"Thị xã Long Mỹ","matp"=>"93"),
	"686" => array("maqh"=>"941","name"=>"Thành phố Sóc Trăng","matp"=>"94"),
	"687" => array("maqh"=>"942","name"=>"Huyện Châu Thành","matp"=>"94"),
	"688" => array("maqh"=>"943","name"=>"Huyện Kế Sách","matp"=>"94"),
	"689" => array("maqh"=>"944","name"=>"Huyện Mỹ Tú","matp"=>"94"),
	"690" => array("maqh"=>"945","name"=>"Huyện Cù Lao Dung","matp"=>"94"),
	"691" => array("maqh"=>"946","name"=>"Huyện Long Phú","matp"=>"94"),
	"692" => array("maqh"=>"947","name"=>"Huyện Mỹ Xuyên","matp"=>"94"),
	"693" => array("maqh"=>"948","name"=>"Thị xã Ngã Năm","matp"=>"94"),
	"694" => array("maqh"=>"949","name"=>"Huyện Thạnh Trị","matp"=>"94"),
	"695" => array("maqh"=>"950","name"=>"Thị xã Vĩnh Châu","matp"=>"94"),
	"696" => array("maqh"=>"951","name"=>"Huyện Trần Đề","matp"=>"94"),
	"697" => array("maqh"=>"954","name"=>"Thành phố Bạc Liêu","matp"=>"95"),
	"698" => array("maqh"=>"956","name"=>"Huyện Hồng Dân","matp"=>"95"),
	"699" => array("maqh"=>"957","name"=>"Huyện Phước Long","matp"=>"95"),
	"700" => array("maqh"=>"958","name"=>"Huyện Vĩnh Lợi","matp"=>"95"),
	"701" => array("maqh"=>"959","name"=>"Thị xã Giá Rai","matp"=>"95"),
	"702" => array("maqh"=>"960","name"=>"Huyện Đông Hải","matp"=>"95"),
	"703" => array("maqh"=>"961","name"=>"Huyện Hoà Bình","matp"=>"95"),
	"704" => array("maqh"=>"964","name"=>"Thành phố Cà Mau","matp"=>"96"),
	"705" => array("maqh"=>"966","name"=>"Huyện U Minh","matp"=>"96"),
	"706" => array("maqh"=>"967","name"=>"Huyện Thới Bình","matp"=>"96"),
	"707" => array("maqh"=>"968","name"=>"Huyện Trần Văn Thời","matp"=>"96"),
	"708" => array("maqh"=>"969","name"=>"Huyện Cái Nước","matp"=>"96"),
	"709" => array("maqh"=>"970","name"=>"Huyện Đầm Dơi","matp"=>"96"),
	"710" => array("maqh"=>"971","name"=>"Huyện Năm Căn","matp"=>"96"),
	"711" => array("maqh"=>"972","name"=>"Huyện Phú Tân","matp"=>"96"),
	"712" => array("maqh"=>"973","name"=>"Huyện Ngọc Hiển","matp"=>"96"),
);