import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class AppColors {
  static const navy      = Color(0xFF0A1628);
  static const navyLight = Color(0xFF112448);
  static const gold      = Color(0xFFC9A84C);
  static const goldLight = Color(0xFFD4B86A);
  static const white     = Color(0xFFFFFFFF);
  static const surface   = Color(0xFFF5F5F7);
  static const surfaceDark = Color(0xFF1C2A3E);
  static const textLight = Color(0xFF6B7280);
  static const error     = Color(0xFFEF4444);
  static const success   = Color(0xFF22C55E);
  static const liveRed   = Color(0xFFDC2626);
}

class AppTheme {
  static ThemeData get light => ThemeData(
    useMaterial3:  true,
    colorScheme:   ColorScheme.fromSeed(
      seedColor:   AppColors.navy,
      primary:     AppColors.navy,
      secondary:   AppColors.gold,
      surface:     AppColors.white,
      background:  AppColors.surface,
    ),
    fontFamily:    'Inter',
    appBarTheme:   const AppBarTheme(
      backgroundColor:    AppColors.navy,
      foregroundColor:    AppColors.white,
      elevation:          0,
      systemOverlayStyle: SystemUiOverlayStyle.light,
      titleTextStyle:     TextStyle(
        fontFamily: 'Inter',
        fontWeight: FontWeight.w600,
        fontSize:   18,
        color:      AppColors.white,
      ),
    ),
    scaffoldBackgroundColor: AppColors.surface,
    cardTheme: CardTheme(
      color:       AppColors.white,
      elevation:   0,
      shape:       RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side:         const BorderSide(color: Color(0xFFE5E7EB)),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.gold,
        foregroundColor: AppColors.navy,
        elevation:       0,
        padding:         const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
        shape:           RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        textStyle:       const TextStyle(fontFamily:'Inter', fontWeight: FontWeight.w700, fontSize:15),
      ),
    ),
    textTheme: const TextTheme(
      displayLarge:  TextStyle(fontFamily:'Inter', fontWeight:FontWeight.w800, color:AppColors.navy),
      headlineMedium:TextStyle(fontFamily:'Inter', fontWeight:FontWeight.w700, color:AppColors.navy),
      titleLarge:    TextStyle(fontFamily:'Inter', fontWeight:FontWeight.w600, color:AppColors.navy),
      bodyLarge:     TextStyle(fontFamily:'Inter', color:Color(0xFF374151)),
      bodyMedium:    TextStyle(fontFamily:'Inter', color:Color(0xFF6B7280)),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled:           true,
      fillColor:        AppColors.white,
      contentPadding:   const EdgeInsets.symmetric(horizontal:16, vertical:14),
      border:           OutlineInputBorder(borderRadius:BorderRadius.circular(12), borderSide:const BorderSide(color:Color(0xFFE5E7EB))),
      enabledBorder:    OutlineInputBorder(borderRadius:BorderRadius.circular(12), borderSide:const BorderSide(color:Color(0xFFE5E7EB))),
      focusedBorder:    OutlineInputBorder(borderRadius:BorderRadius.circular(12), borderSide:const BorderSide(color:AppColors.gold, width:2)),
    ),
  );

  static ThemeData get dark => ThemeData(
    useMaterial3:  true,
    colorScheme:   ColorScheme.fromSeed(
      seedColor:   AppColors.navy,
      brightness:  Brightness.dark,
      primary:     AppColors.gold,
      secondary:   AppColors.gold,
      surface:     AppColors.navyLight,
      background:  AppColors.navy,
    ),
    fontFamily:    'Inter',
    appBarTheme:   const AppBarTheme(
      backgroundColor:    AppColors.navy,
      foregroundColor:    AppColors.white,
      elevation:          0,
      systemOverlayStyle: SystemUiOverlayStyle.light,
    ),
    scaffoldBackgroundColor: AppColors.navy,
    cardTheme: CardTheme(
      color:     AppColors.navyLight,
      elevation: 0,
      shape:     RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side:         const BorderSide(color: Color(0xFF1C2A3E)),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.gold,
        foregroundColor: AppColors.navy,
        elevation:       0,
        padding:         const EdgeInsets.symmetric(horizontal:24, vertical:14),
        shape:           RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    ),
  );
}
